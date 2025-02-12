import json

def lambda_handler(event, context):
    print("event",event)
    # Retrieve data from the esdavent
    message = event['messsage']
    
    
    # Process the message
    processed_message = message.upper()
    
    # Constrfdghuct the response
    response = {
        'statusCode': 200,
        'body': json.dumps({
            'message': processed_message
        })
    }
    
    return response
