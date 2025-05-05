import json
import os
from stcore import pms

#test1234
def update_status(wcnx, trigger_source, user_attrs):
    try:
        sql = """
            INSERT INTO cognito_logs (
                event_type,
                email,
                username,
                store_name,
                store_id,
                member_id,
                badge_id,
                name
            ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
        """
        with wcnx.cursor() as cursor:
            cursor.execute(sql, (
                trigger_source,
                user_attrs.get('email'),
                user_attrs.get('sub'),
                user_attrs.get('custom:storeName'),
                user_attrs.get('custom:storeId'),
                user_attrs.get('custom:memberId'),
                user_attrs.get('custom:badgeId'),
                user_attrs.get('name')
            ))
            wcnx.commit()
            print(f"Logged Cognito event: {trigger_source} for {user_attrs.get('email')}")
    except Exception as e:
        print(f"Error inserting log: {e}")
        wcnx.rollback()


def lambda_handler(event, context):
    trigger_source = event['triggerSource']
    user_attrs = event['request']['userAttributes']

    print("Trigger source:", trigger_source)
    print("User attributes:", json.dumps(user_attrs))

    # Get DB connection and insert log
    try:
        wcnx = pms.get_writer_cnx()
        update_status(wcnx, trigger_source, user_attrs)
    except Exception as e:
        print(f"DB connection or log failed: {e}")
    finally:
        if wcnx:
            wcnx.close()

    return event













# import json
# import boto3

# sender_email = 'sanjog.k@shikhartech.com'

# def lambda_handler(event, context):
#     json_data = {
#   "order": {
#     "id": "12345",
#     "created_at": "2025-02-28T10:00:00Z",
#     "shipping_method": "Express Delivery",
#     "shipping_cost": "10.00",
#     "order_items": [
#       {
#         "name": "Wireless Headphones",
#         "quantity": 1,
#         "size": "Large",
#         "total": "50.00",
#         "currency_code": "USD"
#       },
#       {
#         "name": "Smartphone Case",
#         "quantity": 2,
#         "size": "Medium",
#         "total": "15.00",
#         "currency_code": "USD"
#       }
#     ],
#     "shipping_address": {
#       "first_name": "John",
#       "last_name": "Doe",
#       "address1": "123 Main Street",
#       "city": "Kathmandu",
#       "state": "Bagmati",
#       "country": "Nepal",
#       "zip": "44600"
#     },
#     "member_name": "John Doe"
#   },
#   "store": {
#     "store_url": "shikharstore.com",
#     "logo": "logo.png"
#   },
#   "date_time": "2025-02-28 10:00:00",
#   "public_path": "https://www.shikharstore.com",
#   "custom_message": "Thank you for being with us  ",
#   "member_name": "John Doe",
#   "status": "this is status"
# }
    
#     destinations = [{
#         "Destination": {
#             "ToAddresses": ['khajumsanjog@gmail.com']
#         },
#         "ReplacementTemplateData": json.dumps(json_data)
#     }]
   
#     # Send the email using Amazon SES
#     send_email(destinations, json_data)

#     return {
#         'statusCode': 200,
#         'body': 'Emails sent successfully'
#     }

# def send_email(destinations, json_data):
#     ses_client = boto3.client('ses', region_name='us-west-1')  # Update with your desired region
    
#     response = ses_client.send_bulk_templated_email(
#         Source=sender_email,
#         Destinations=destinations,
#         Template='custom_message',
#         DefaultTemplateData=json.dumps(json_data) 
#     ) 
#     print(response)




# import json
# from stcore import pms

# def execute_query(wcnx, sql):
#     with wcnx.cursor() as cursor:
#         cursor.execute(sql)

#         result = cursor.fetchall()
#         return result if result else None

# def insert_db(wcnx, extracted_data):
#     cursor = wcnx.cursor()
    
#     # SQL query to insert data
#     sql_insert_query = """
#     INSERT INTO guardduty_findings (
#         id, version, detail_type, source, account, time, region,
#         schema_version, account_id, region_detail, type, instance_id,
#         instance_type, launch_time, network_interface_id, private_ip,
#         public_ip, security_group, service_name, detector_id,
#         remote_ip, remote_port, severity, title, description,
#         created_at, updated_at
#     ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s,
#               %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
#     """

#     # Values to be inserted
#     insert_values = (
#         extracted_data['id'],
#         extracted_data['version'],
#         extracted_data['detail_type'],
#         extracted_data['source'],
#         extracted_data['account'],
#         extracted_data['time'],
#         extracted_data['region'],
#         extracted_data['schema_version'],
#         extracted_data['account_id'],
#         extracted_data['region_detail'],
#         extracted_data['type'],
#         extracted_data['instance_id'],
#         extracted_data['instance_type'],
#         extracted_data['launch_time'],
#         extracted_data['network_interface_id'],
#         extracted_data['private_ip'],
#         extracted_data['public_ip'],
#         extracted_data['security_group'],
#         extracted_data['service_name'],
#         extracted_data['detector_id'],
#         extracted_data['remote_ip'],
#         extracted_data['remote_port'],
#         extracted_data['severity'],
#         extracted_data['title'],
#         extracted_data['description'],
#         extracted_data.get('created_at', 'NOW()'),  # Default to NOW() if not provided
#         extracted_data.get('updated_at', 'NOW()')   # Default to NOW() if not provided
#     )
#     try:
#         cursor.execute(sql_insert_query, insert_values)
#         wcnx.commit()
#         print("Record inserted successfully")
#     except Exception as e:
#         print("Error inserting record:", e)
#         wcnx.rollback()
#     finally:
#         cursor.close()



# def lambda_handler(event, context):
#     print("event ko data", event)
    
#     # Use the event directly if it does not have 'Records'
#     if 'Records' in event:
#         extracted_data = json.loads(event['Records'][0]['body'])
#     else:
#         extracted_data = event  # Use the event data directly if no Records

#     # Log the extracted data for debugging
#     print("Extracted data before processing:", json.dumps(extracted_data, indent=2))
    
#     wcnx = pms.get_writer_cnx()

#     # Check if the connection is established
#     if wcnx:
#         print("Database connection established.")
#     else:
#         print("Failed to establish database connection.")
#         return  # Exit if the connection was not established

#     # Ensure extracted_data is a dictionary
#     if not isinstance(extracted_data, dict):
#         print("Error: extracted_data is not a dictionary:", extracted_data)
#         return

#     # Proceed with extracting required fields
#     extracted_data = {
#         'version': extracted_data.get('version'),
#         'id': extracted_data.get('id'),
#         'detail_type': extracted_data.get('detail-type'),
#         'source': extracted_data.get('source'),
#         'account': extracted_data.get('account'),
#         'time': extracted_data.get('time'),
#         'region': extracted_data.get('region'),
#         'schema_version': extracted_data.get('detail', {}).get('schemaVersion'),
#         'account_id': extracted_data.get('detail', {}).get('accountId'),
#         'region_detail': extracted_data.get('detail', {}).get('region'),
#         'type': extracted_data.get('detail', {}).get('type'),
#         'instance_id': extracted_data.get('detail', {}).get('resource', {}).get('instanceDetails', {}).get('instanceId'),
#         'instance_type': extracted_data.get('detail', {}).get('resource', {}).get('instanceDetails', {}).get('instanceType'),
#         'launch_time': extracted_data.get('detail', {}).get('resource', {}).get('instanceDetails', {}).get('launchTime'),
#         'network_interface_id': extracted_data.get('detail', {}).get('resource', {}).get('instanceDetails', {}).get('networkInterfaces', [{}])[0].get('networkInterfaceId'),
#         'private_ip': extracted_data.get('detail', {}).get('resource', {}).get('instanceDetails', {}).get('networkInterfaces', [{}])[0].get('privateIpAddress'),
#         'public_ip': extracted_data.get('detail', {}).get('resource', {}).get('instanceDetails', {}).get('networkInterfaces', [{}])[0].get('publicIp'),
#         'security_group': extracted_data.get('detail', {}).get('resource', {}).get('instanceDetails', {}).get('networkInterfaces', [{}])[0].get('securityGroups', [{}])[0].get('groupName'),
#         'service_name': extracted_data.get('detail', {}).get('service', {}).get('serviceName'),
#         'detector_id': extracted_data.get('detail', {}).get('service', {}).get('detectorId'),
#         'remote_ip': extracted_data.get('detail', {}).get('service', {}).get('action', {}).get('networkConnectionAction', {}).get('remoteIpDetails', {}).get('ipAddressV4'),
#         'remote_port': extracted_data.get('detail', {}).get('service', {}).get('action', {}).get('networkConnectionAction', {}).get('remotePortDetails', {}).get('port'),
#         'severity': extracted_data.get('detail', {}).get('severity'),
#         'title': extracted_data.get('detail', {}).get('title'),
#         'description': extracted_data.get('detail', {}).get('description'),
#         'created_at': extracted_data.get('detail', {}).get('createdAt'),
#         'updated_at': extracted_data.get('detail', {}).get('updatedAt'),
#     }

#     # Log the extracted fields to verify they are correct
#     print("Final extracted data for DB insertion:", json.dumps(extracted_data, indent=2))

#     # Call insert_db for each extracted_data
#     insert_db(wcnx, extracted_data)

#     # Close the connection if you don't need it anymore
#     try:
#         if wcnx:  # Check if wcnx is a valid connection object
#             wcnx.close()  # Close the connection unconditionally
#             print("Connection closed.")
#         else:
#             print("No connection to close.")
#     except Exception as e:
#         print(f"Error while closing the connection: {e}")
