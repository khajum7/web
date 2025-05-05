import json
import sys
import importlib
from datetime import datetime
from stcore import pms

# Import the required webhook files
showroom_webhook = importlib.import_module('showroom_webhook')
voxcatalog_webhook = importlib.import_module('voxcatalog_webhook')

def lambda_handler(event, context):

    wcnx = pms.get_writer_cnx()
    wcnx2 = pms.get_writer_cnx(env_key_prefix='DB2')
    for record in event['Records']:
        sns_message = record['Sns']['Message']
        sns_data = json.loads(sns_message)
        
        # Extract the configuration-set from the headers
        headers = sns_data.get('mail', {}).get('headers', [])
        configuration_set = None
        
        
        # Loop through headers to find the X-SES-CONFIGURATION-SET
        for header in headers:
            if header.get('name') == 'X-SES-CONFIGURATION-SET':
                configuration_set = header.get('value')
                break
        
        # Check if configuration-set matches showroom or voxcatalog
        if configuration_set == "showroom-configuration-sets-STAGING":
            # Send to showroom webhook
            message = showroom_webhook.construct_message_showroom(sns_data, wcnx)
            print(sns_data)
            print(f"Sent to showroom webhook: {message}")
        elif configuration_set == "Voxcatalog-configuration-sets-STAGING":
            # Send to voxcatalog webhook
            message = voxcatalog_webhook.construct_message_voxcatalog(sns_data, wcnx2)
            print(f"Sent to voxcatalog webhook: {message}")
        else:
            print(f"Unknown configuration-set: {configuration_set}")
    
    return {
        'statusCode': 200,
        'body': json.dumps('Successfully processed the SNS event')
    }














# import json
# from pytz import timezone
# from os import getenv
# from stcore import pms
# from datetime import datetime
# from showroom_webhook import 
# from voxcatalog_webhook import




# def lambda_handler(event, context):
#     print("Received event:", json.dumps(event))
    
#     message = json.loads(event['Records'][0]['Sns']['Message'])
    
#     wcnx = pms.get_writer_cnx()
#     wcnx2 = pms.get_writer_cnx(env_key_prefix='DB2')
#     print(wcnx2)
#     print(wcnx)
#     return

    
#     try:
#         message = construct_message(message, wcnx2)
#     except:
#         raise
#     finally:
#         wcnx.close()

#     return {'statusCode': 200, 'body': message}