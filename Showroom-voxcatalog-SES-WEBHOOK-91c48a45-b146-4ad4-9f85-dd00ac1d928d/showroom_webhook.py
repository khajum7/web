import json
from pytz import timezone
from os import getenv
from stcore import pms
from datetime import datetime

#test2345

#This function is called when a new message is received from sns on lambda handler and then this function will call construct_message_showroom function to store the eventtype of SES message.
def construct_message_showroom(message, wcnx):

    message_id = message['mail']['messageId']
    destination = message['mail']['destination'][0]
    today_date = datetime.now(timezone('America/Denver')).strftime('%Y-%m-%d %H:%M:%S')

    event_type = message['eventType']

    sql = """INSERT INTO `notification_logs` 
             (`notification_type`, `destination`, `message_id`, `created_at`) 
             VALUES (%s, %s, %s, %s)"""


    with wcnx.cursor() as cursor:
        cursor.execute(sql, (event_type, destination, message_id, today_date))
        wcnx.commit()


