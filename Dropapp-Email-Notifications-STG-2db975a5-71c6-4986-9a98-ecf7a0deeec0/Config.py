import json
from datetime import datetime
from inspect import cleandoc

import boto3
from pytz import timezone
from stcore import pms

from datetime import datetime  


# Email source for sending notifications test 1
MAIL_SOURCE = 'notifications@getcultureshock.com'

# Mapping order statuses to email templates and subjects
TEMPLATE_MAP = {
    '0': ('dropapp_new_order', 'New Order'),
    '1': ('dropapp_order_cancelled', 'Order Cancelled'),
    '3': ('dropapp_raffle_order_confirmation', 'Order Shipped'),
    '2': ('dropapp_order_shipped', 'Order Shipped'),
}

# Default data for email template
DEFAULT_DATA = {}

# Public path for external resources
PUBLIC_PATH = "https://dropappdev.voxmg.com"


def execute_query(cnx, sql, fetch=0):
    """
    Executes the given SQL query and fetches results based on the fetch mode.

    Args:
        cnx: Database connection object.
        sql (str): SQL query to execute.
        fetch (int): 0 for fetchall(), 1 for fetchone().

    Returns:
        dict or list: Query result in dictionary or list format.
    """
    with cnx.cursor() as cursor:
        cursor.execute(sql)

    if fetch == 1:
        result = cursor.fetchone()
        return {} if result is None else result
    else:
        result = cursor.fetchall()
        return result if result else []


def chunk(data, size):
    for i in range(0, len(data), size):
        yield data[i:i + size]


def insert_logs(wcnx, data, publishId = '', creditId = 0):
    """
    Inserts log entries into the `member_notifications_logs` table.

    Args:
        wcnx: Write database connection object.
        data (list): List of dictionaries containing log data.
    """

    timestamp = datetime.now(
        timezone('America/Denver')).strftime('%Y-%m-%d %H:%M:%S')

    sql = cleandoc(f'''
        INSERT INTO `member_notifications_logs`
            (`customerId`, `memberId`, `productId`, `productName`, `comment`, `type`, `created_at`, `saleId`, `event`, `badgeId`, `updated_at`, `channel`)
        VALUES
            (%(customerId)s, %(memberId)s, %(productId)s, %(productName)s, '{publishId}', %(type)s, '{timestamp}', %(saleId)s, %(event)s, %(badgeId)s, '{timestamp}', '0')
    ''')

    print(sql)

    with wcnx.cursor() as cursor:
        cursor.executemany(sql, data)
        wcnx.commit()


def error_response(message):
    return {
        'statusCode': 202,
        'body': json.dumps({'status': 'error', 'message': message})
    }


def success_response(message):
    return {
        'statusCode': 200,
        'body': json.dumps({'status': 'success', 'message': message})
    }


def send_bulk_mail(template, customer, destinations):
    """
    Sends bulk templated emails using AWS SES.

    Args:
        template (str): Email template name.
        customer (dict): Customer details.
        destinations (list): List of recipient details.
    """
    ses_client = boto3.client('ses')

    mail_from = f'''{customer['name']}<{MAIL_SOURCE}>'''

    return ses_client.send_bulk_templated_email(
        Source=mail_from,
        Destinations=destinations,
        Template=template,
        DefaultTemplateData=json.dumps(DEFAULT_DATA),
        ConfigurationSetName='Dropapp-Config-set-STG'
    )

def list_to_tuple(list):
    '''Function to convert a list to a tuple'''
    # Convert list to SQL-compatible tuple
    return f"('{list[0]}')" if (len(list) < 2) else f'{tuple(list)}'
