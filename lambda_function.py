import json

def lambda_handler(event, context):
    # Retrieve data from the event
    message = event['message']
    
    # Process the message
    processed_message = message.upper()
    
    # Construct the response
    response = {
        'statusCode': 200,
        'body': json.dumps({
            'message': processed_message
        })
    }
    
    return response
