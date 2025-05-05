import json
from datetime import datetime
from pytz import timezone

from inspect import cleandoc

from Config import execute_query, error_response, success_response, send_bulk_mail, chunk, insert_logs, list_to_tuple

SUBJECT = 'Credits rewarded'
# Mapping order statuses to email templates and subjects
TEMPLATE_NAME = "dropapp_order_credit"
# Public path for external resources
PUBLIC_PATH = "https://dropappdev.voxmg.com"

def prepareInserts(customerId, memberId, badgeId):
    """
    Prepares a dictionary containing credit reward details to be inserted into logs.
    """
    return {
        'customerId': customerId,
        'memberId': memberId,
        'saleId': 0,
        'productId': 0,
        'productName': '',
        'type': 'Credit Reward',
        'event': 'Email:Credit Rewarded',
        'badgeId': badgeId,
    }

def prepareDestinations(customer, name, email, addBalance, currentBalance, creditName, today_date, memberId):
    """
    Prepares email notification data.

    Returns:
        dict: Dictionary formatted for email sending.
    """
    data = {
        'customer': customer,
        'member': {
            'name': name,
            'email': email,
        },
        'credit': {
            'creditType': creditName,
            'addBalance': addBalance,
            'currentBalance': currentBalance
        },
        'publicPath': PUBLIC_PATH,
        'subject': SUBJECT,
        'dateTime': today_date,
    }

    print(json.dumps(data, indent=2, default=str))
    return {
        "Destination": {
            "ToAddresses": [email]
        },
        "ReplacementTemplateData": json.dumps(data, default=str),
        "ReplacementTags": [
            {"Name": "customerId", "Value": str(customer.get('id'))},
            {"Name": "saleId", "Value": str("NULL")},
            {"Name": "productId", "Value": str("NULL")},
            {"Name": "memberId", "Value": str(memberId)},
        ]
    }

class CreditNotification:
    """Handles credit notification processes for customers and members."""

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


    def getCustomer(self):
        """Fetches customer details from the database."""
        sql = cleandoc(
            f"SELECT `id`, `name`, concat('/logos/', `logo`) as logo FROM `customers` WHERE `id` = '{self.customerId}' AND status = '1' LIMIT 1 ")
        return execute_query(self.cnx, sql, fetch=1)


    def getTempCredits(self, batchCode):
        """
        Retrieves temporary credit records for processing.

        Args:
            batchCode (str): Unique batch identifier.

        Returns:
            list: List of temporary credit records.
        """
        sql = f'''
        SELECT *,
        (SELECT creditType FROM credit_types WHERE credit_types.id = `creditId` LIMIT 1) AS creditType,
        (SELECT credit FROM member_credits WHERE member_credits.creditId = csv_temp_credits.`creditId` LIMIT 1) AS currentCredit,
        members.name, members.email, members.badgeId, members.id as memberId
        FROM `csv_temp_credits`
        JOIN members ON members.`badgeId` = csv_temp_credits.`badgeId` AND members.customerId = {self.customerId}
        WHERE
            `batchCode` = '{batchCode}'
            AND csv_temp_credits.`status` = '2'
          '''

        return execute_query(self.cnx, sql)

    def updateEmailFlag(self, flag, id, table='csv_temp_credits', check = '='):
        """
        Updates the email notification flag for a record.

        Args:
            flag (int): Flag status (e.g., 1 for processing, 2 for notified).
            id (int or list): Record ID(s) to update.
            table (str): Table name where the update is to be applied.
            check (str): Comparison operator (default is '=' for single ID, 'IN' for multiple IDs).
        """
        sql = f"UPDATE `{table}` SET `emailNotify` = '{flag}' WHERE `id` {check} {id}"

        execute_query(self.wcnx, sql)
        self.wcnx.commit()

    def getCreditLog(self, id):
        """
        Retrieves credit log details for a given credit transaction.

        Args:
            id (int): Credit log ID.

        Returns:
            dict: Credit log details.
        """
        sql = f'''
        SELECT
            currentCreditAmount,
            creditAddedAmount,
            newFinalAmount,
            members.name,
            members.email,
            members.badgeId,
            memberId,
            credit_logs.emailNotify,
            credit_logs.creditType,
            (
                SELECT creditType
                FROM credit_types
                WHERE
                    credit_types.id = credit_logs.`creditType`
                LIMIT 1
            ) AS creditTypeName
        FROM credit_logs
            JOIN members ON members.id = credit_logs.memberId
        WHERE
            credit_logs.id = {id}
            AND credit_logs.customerId = {self.customerId}
        '''

        return execute_query(self.cnx, sql, fetch=1)


    def processBatchCredits(self, batchCode):
        """
        Processes batch credit rewards and sends notifications.

        Args:
            batchCode (str): Batch code of credit transactions.

        Returns:
            dict: Response indicating success or failure.
        """
        tempCredits = self.getTempCredits(batchCode)

        if not len(tempCredits):
            return error_response('No records found for batch code')

        customer = self.getCustomer()
        today_date = datetime.now(timezone('America/Denver')).strftime('%Y-%m-%d %H:%M:%S')

        for chunked in chunk(tempCredits, 50):
            destinations = []
            inserts = []
            ids = []
            creditId = 0

            for tempCredit in chunked:
                tempCreditId = tempCredit.get('id')

                if(tempCredit.get('emailNotify') != '0'):
                    print('Already notified, id:', tempCreditId)
                    continue

                ids.extend([tempCreditId])

                self.updateEmailFlag('1', tempCreditId)
                email = tempCredit.get('email')
                name = tempCredit.get('name')
                creditName = tempCredit.get('creditType')
                addBalance = tempCredit.get('creditAmount')
                currentBalance = tempCredit.get('currentCredit')
                creditId = tempCredit.get('creditId')

                destinations.append(prepareDestinations(customer=customer, name=name, creditName=creditName, email=email,addBalance=addBalance, currentBalance=currentBalance, today_date=today_date, memberId=tempCredit.get('memberId')))

                logs = prepareInserts(self.customerId, tempCredit.get('memberId'), tempCredit.get('badgeId'))
                inserts.append(logs)

            print(destinations)
            publishId = send_bulk_mail(TEMPLATE_NAME, customer=customer, destinations=destinations)['Status'][0]['MessageId']
            print
            print("insert", inserts)
            insert_logs(self.wcnx, inserts, publishId=publishId, creditId=creditId)
            self.updateEmailFlag('2', list_to_tuple(ids), check='IN')

        return success_response('Notified successfully')


    def processSingleCredit(self):
        """
        Processes a single credit log entry and sends an email notification if not already notified.

        Returns:
            dict: A success response if the notification is sent successfully, or an error response if already notified.
        """
        logId = self.data.get('logId')
        log = self.getCreditLog(logId)

        if log.get('emailNotify') != '0':
            return error_response('Already notified')

        self.updateEmailFlag('1', logId, table='credit_logs')

        customer = self.getCustomer()

        email = log.get('email')
        name = log.get('name')
        memberId = log.get('memberId')
        badgeId = log.get('badgeId')
        creditName = log.get('creditTypeName')
        addBalance = log.get('creditAddedAmount')
        currentBalance = log.get('newFinalAmount')
        today_date = datetime.now(timezone('America/Denver')).strftime('%Y-%m-%d %H:%M:%S')

        destinations = [prepareDestinations(customer=customer, name=name, creditName=creditName,
                                                email=email, addBalance=addBalance, currentBalance=currentBalance, today_date=today_date, memberId=memberId)]

        print(destinations)
        inserts = [prepareInserts(self.customerId, memberId=memberId, badgeId=badgeId)]
        publishId = send_bulk_mail(TEMPLATE_NAME, customer=customer, destinations=destinations)['Status'][0]['MessageId']
        # publishId = 1231
        insert_logs(self.wcnx, data=inserts, publishId=publishId, creditId=log.get('creditType'))
        self.updateEmailFlag('2', logId, table='credit_logs')

        return success_response('Notified successfully')



    def notify(self):
        """Processes and sends credit notifications."""
        batchCode = self.data.get('batchCode')
        if batchCode:
            return self.processBatchCredits(batchCode)
        else:
            return self.processSingleCredit()