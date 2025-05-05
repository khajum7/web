import json
from datetime import datetime
from inspect import cleandoc
from pytz import timezone
from Config import execute_query, error_response, success_response, send_bulk_mail, insert_logs


# Mapping order statuses to email templates and subjects
TEMPLATE_MAP = {
    '0': ('dropapp_new_order', 'New Order'),
    '1': ('dropapp_order_cancelled', 'Order Cancelled'),
    '3': ('dropapp_raffle_order_confirmation', 'Raffle Participation Confirmation'),
    '2': ('dropapp_order_shipped', 'Order Shipped'),
}

# Public path for external resources
PUBLIC_PATH = "https://dropappdev.voxmg.com"

class OrderNotification:
    """Handles order-related notifications."""

    def __init__(self, cnx, wcnx, data):
        """
        Initializes the order notification class.

        Args:
            cnx: Read database connection object.
            wcnx: Write database connection object.
            data (dict): Order data.
        """
        self.cnx = cnx
        self.wcnx = wcnx
        self.data = data
        self.customerId = data.get("customerId")
        self.memberId = data.get("memberId")
        self.saleId = data.get("saleId")

    def getSale(self):
        """Fetches sale details from the database."""
        sql = cleandoc(f'''
            SELECT `id`, `customerId`, `memberId`, `badgeId`, `name`, `email`, `status`, `creditType`, `shipMethod`, `price`, `isRaffle`,
                `usedShipMethod`,  `created_at`, `emailNotify`
            FROM `sales` WHERE `id` = '{self.saleId}' AND `customerId` = '{self.customerId}' LIMIT 1
        ''')
        return execute_query(self.cnx, sql, fetch=1)

    def updateSale(self, flag):
        """Updates the sale's email notification flag."""
        sql = f"UPDATE `sales` SET `emailNotify` = '{flag}' WHERE `id` = '{self.saleId}' AND `customerId` = '{self.customerId}'"
        print("updateSale", sql)
        with self.wcnx.cursor() as cursor:
            cursor.execute(sql)
            self.wcnx.commit()

    def getCustomer(self):
        """Fetches customer details from the database."""
        sql = cleandoc(
            f"SELECT `id`, `name`, concat('/logos/', `logo`) as logo FROM `customers` WHERE `id` = '{self.customerId}' AND status = '1' LIMIT 1 ")
        return execute_query(self.cnx, sql, fetch=1)

    def getLineItems(self):
        """Fetches purchased items for the sale."""
        sql = f"SELECT quantity, name, productId FROM `purchased_items` WHERE `saleId` = {self.saleId}"
        return execute_query(self.wcnx, sql)

    def getShippingAddress(self):
        """Fetches the shipping address for the sale."""
        sql = f"SELECT firstName, IFNULL(lastName, '') AS lastName, address1, IFNULL(address2, '') AS address2, city, state, zip, country, phoneNumber as phone1 FROM `shipping_addresses` WHERE `saleId` = {self.saleId}"
        return execute_query(self.wcnx, sql, fetch=1) or None

    def notify(self):
        """Processes and sends order notifications."""
        sale = self.getSale()

        if not sale:
            return error_response('Order not found.')
        if sale.get('emailNotify') not in [0, '0']:
            return error_response('Order is already processed.')

        self.updateSale(1) # Update email notification flag marking as processed

        customer = self.getCustomer()

        # get purchased products
        lineItems = self.getLineItems()
        if not lineItems:
            return error_response('Line items not found.')

        # get shipping address
        shippingAddress = self.getShippingAddress()
        if not shippingAddress:
            return error_response('Shipping address not found.')

        saleStatus = str(sale['status'])
        template, subject = TEMPLATE_MAP.get(saleStatus, ('', '')) # get template and subject based on sale status
        print("template_name", template)
        today_date = datetime.now(timezone('America/Denver')).strftime('%m-%d-%Y %I:%M %p')
        data = {
            'customer': customer,
            'sale': {
                'id': sale.get('id'),
                'memberName': sale.get('name'),
                'memberEmail': sale.get('email'),
                'shippingMethod': sale.get('shipMethod'),
                'price': sale.get('price'),
                'creditType': sale.get('creditType'),
                'createdAt': sale.get('created_at').strftime('%m-%d-%Y %I:%M %p'),
                'isRaffle': bool(int(sale.get('isRaffle'))),
                'purchasedItems': lineItems,
                'shippingAddress': shippingAddress,
            },
            'publicPath': PUBLIC_PATH,
            'subject': subject,
            'dateTime': today_date,
        }
        print(json.dumps(data, default=str, indent=2))


        destinations = [{
            "Destination": {
                "ToAddresses": [sale.get('email')]
                #  "ToAddresses": ['khajumsanjog@gmail.com', 'bijayasanwa@outlook.com', 'niraj.s@shikhartech.com']
            },
            "ReplacementTemplateData": json.dumps(data, default=str),
            "ReplacementTags": [
                {"Name": "customerId", "Value": str(customer.get('id'))},
                {"Name": "saleId", "Value": str(sale.get('id'))},
                {"Name": "productId", "Value": str("NULL")},
                {"Name": "memberId", "Value": str(sale.get('memberId'))},
            ]
        }]

        print("destinations", destinations)

        # send email
        published_id = send_bulk_mail(template, customer, destinations)['Status'][0]['MessageId']

        inserts = [{
            'customerId': self.customerId,
            'memberId': self.memberId,
            'saleId': self.saleId,
            'productId': lineItems[0].get('productId'),
            'productName': lineItems[0].get('name'),
            'type': 'order',
            'event': subject,
            'badgeId': sale.get('badgeId'),
        }]

        self.updateSale(2) # Update email notification flag marking as sent
        insert_logs(self.wcnx, inserts, published_id) # insert log
        return success_response('Email sent successfully.')



# import json
# from datetime import datetime
# from inspect import cleandoc

# from Config import execute_query, error_response, success_response, send_bulk_mail, insert_logs


# # Mapping order statuses to email templates and subjects
# TEMPLATE_MAP = {
#     '0': ('dropapp_new_order', 'New Order'),
#     '1': ('dropapp_order_cancelled', 'Order Cancelled'),
#     '3': ('dropapp_raffle_order_confirmation', 'Raffle Participation Confirmation'),
#     '2': ('dropapp_order_shipped', 'Order Shipped'),
# }

# # Public path for external resources
# PUBLIC_PATH = "https://dropappdev.voxmg.com"

# class OrderNotification:
#     """Handles order-related notifications."""

#     def __init__(self, cnx, wcnx, data):
#         """
#         Initializes the order notification class.

#         Args:
#             cnx: Read database connection object.
#             wcnx: Write database connection object.
#             data (dict): Order data.
#         """
#         self.cnx = cnx
#         self.wcnx = wcnx
#         self.data = data
#         self.customerId = data.get("customerId")
#         self.memberId = data.get("memberId")
#         self.saleId = data.get("saleId")

#     def getSale(self):
#         """Fetches sale details from the database."""
#         sql = cleandoc(f'''
#             SELECT `id`, `customerId`, `memberId`, `badgeId`, `name`, `email`, `status`, `creditType`, `shipMethod`, `price`, `isRaffle`,
#                 `usedShipMethod`,  `created_at`, `emailNotify`
#             FROM `sales` WHERE `id` = '{self.saleId}' AND `customerId` = '{self.customerId}' LIMIT 1
#         ''')
#         return execute_query(self.cnx, sql, fetch=1)

#     def updateSale(self, flag):
#         """Updates the sale's email notification flag."""
#         sql = f"UPDATE `sales` SET `emailNotify` = '{flag}' WHERE `id` = '{self.saleId}'"
#         print("updateSale", sql)
#         with self.wcnx.cursor() as cursor:
#             cursor.execute(sql)
#             self.wcnx.commit()

#     def getCustomer(self):
#         """Fetches customer details from the database."""
#         sql = cleandoc(
#             f"SELECT `id`, `name`, concat('/logos/', `logo`) as logo FROM `customers` WHERE `id` = '{self.customerId}' AND status = '1' LIMIT 1 ")
#         return execute_query(self.cnx, sql, fetch=1)

#     def getLineItems(self):
#         """Fetches purchased items for the sale."""
#         sql = f"SELECT quantity, name, productId FROM `purchased_items` WHERE `saleId` = {self.saleId}"
#         return execute_query(self.wcnx, sql)

#     def getShippingAddress(self):
#         """Fetches the shipping address for the sale."""
#         sql = f"SELECT firstName, lastName, address1, address2, city, state, zip, country, phoneNumber as phone1 FROM `shipping_addresses` WHERE `saleId` = {self.saleId}"
#         return execute_query(self.wcnx, sql, fetch=1) or None

#     def notify(self):
#         """Processes and sends order notifications."""
#         sale = self.getSale()

#         if not sale:
#             return error_response('Order not found.')
#         if sale.get('emailNotify') not in [0, '0']:
#             return error_response('Order is already processed.')

#         self.updateSale(1) # Update email notification flag marking as processed

#         customer = self.getCustomer()

#         # get purchased products
#         lineItems = self.getLineItems()
#         if not lineItems:
#             return error_response('Line items not found.')

#         # get shipping address
#         shippingAddress = self.getShippingAddress()
#         if not shippingAddress:
#             return error_response('Shipping address not found.')

#         saleStatus = str(sale['status'])
#         template, subject = TEMPLATE_MAP.get(saleStatus, ('', '')) # get template and subject based on sale status
#         print("template_name", template)

#         data = {
#             'customer': customer,
#             'sale': {
#                 'id': sale.get('id'),
#                 'memberName': sale.get('name'),
#                 'memberEmail': sale.get('email'),
#                 'shippingMethod': sale.get('shipMethod'),
#                 'price': sale.get('price'),
#                 'creditType': sale.get('creditType'),
#                 'createdAt': sale.get('created_at').strftime('%m-%d-%Y %I:%M %p'),
#                 'isRaffle': bool(int(sale.get('isRaffle'))),
#                 'purchasedItems': lineItems,
#                 'shippingAddress': shippingAddress,
#             },
#             'publicPath': PUBLIC_PATH,
#             'subject': subject
#         }
#         print(json.dumps(data, default=str, indent=2))


#         destinations = [{
#             "Destination": {
#                 # "ToAddresses": [sale.get('email')]
#                  "ToAddresses": ['khajumsanjog@gmail.com', 'bijayasanwa@outlook.com', 'niraj.s@shikhartech.com']
#             },
#             "ReplacementTemplateData": json.dumps(data, default=str),
#             "ReplacementTags": [
#                 {"Name": "customerId", "Value": str(customer.get('id'))},
#                 {"Name": "saleId", "Value": str(sale.get('id'))},
#                 {"Name": "productId", "Value": str("NULL")},
#                 {"Name": "memberId", "Value": str(sale.get('memberId'))},
#             ]
#         }]

#         print("destinations", destinations)

#         # send email
#         published_id = send_bulk_mail(template, customer, destinations)['Status'][0]['MessageId']

#         inserts = [{
#             'customerId': self.customerId,
#             'memberId': self.memberId,
#             'saleId': self.saleId,
#             'productId': lineItems[0].get('productId'),
#             'productName': lineItems[0].get('name'),
#             'type': 'order',
#             'event': f'Email:{subject}',
#             'badgeId': sale.get('badgeId'),
#         }]

#         self.updateSale(2) # Update email notification flag marking as sent
#         insert_logs(self.wcnx, inserts, published_id) # insert log
#         return success_response('Email sent successfully.')