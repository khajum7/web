import json

from stcore import pms

from Order import OrderNotification
from Credits import CreditNotification

def lambda_handler(event, context):
    print("event=>", event)
    data = json.loads(event['Records'][0]['Sns']['Message'])
    cnx = pms.get_reader_cnx('snkrs')
    wcnx = pms.get_writer_cnx('snkrs')

    try:
        type = data.get("type")
        print(type)
        if (type == 'order'):
            targetClass = OrderNotification(cnx, wcnx, data)
        elif (type == "credit"):
            targetClass = CreditNotification(cnx, wcnx, data)

        response = targetClass.notify()
        print(response)
    except Exception as e:
        raise
    #     print(e)
    #     raise
    # finally:
    #     cnx.close()
    #     wcnx.close()


# order
# data = {
#     'customerId': 15,
#     'memberId': 7750,
#     'saleId': 44686,
#     'type': 'order'
# }

# credit
# data = {
#     'batchCode': 1575322317,
#     'type': 'credit',
#     'customerId': 15
# }
# data = {
#     'logId': 263175,
#     'customerId': 15,
#         'type': 'credit',
# }


# event = {
#     'Records': [{
#         'body': json.dumps(data),
#     }]
# }
# event = {
#     'Records': [{
#         "Sns": {
#             "Message": json.dumps(data)
#         }
#     }]
# }

# lambda_handler(event, 0)