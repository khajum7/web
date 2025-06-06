import json
import requests

def lambda_handler(event, context):
    print(event)
    print(hellothere)
    
    sns_message = event['Records'][0]['Sns']['Message']
    message_json = json.loads(sns_message)

    execution_detail = message_json.get('detail', {})
    
    
    #added condition
    pipeline_state = execution_detail.get('state', '')
    if pipeline_state == 'FAILED':
        print(f"Pipeline state is {pipeline_state}, skipping Lambda execution.")
        return
    
    git_details = execution_detail.get('execution-trigger', {})
    
    repository_name = git_details.get('full-repository-name', 'notavailable')
    branch_name = git_details.get('branch-name', 'notavailable')
    commit_id = git_details.get('commit-id', 'notavailable')
    execution_id = execution_detail.get('execution-id', 'notavailable')
    pipeline = execution_detail.get('pipeline', 'notavailable')
    print("branch name =>", branch_name)
    
    print(commit_id)
    
    tdms_api_url = f'https://tdms.shikhartech.com/tasks/{commit_id}/status/{branch_name}?apiToken=I1LO1beNg&execution={execution_id}&pipeline={pipeline}'

    print(tdms_api_url)
    
    pulls_response = requests.get(tdms_api_url)
    
    pulls_response = pulls_response.json()
    print("Pull-response =>", pulls_response)
    
    if pulls_response['code'] != 200:
        return
    
    if branch_name == 'production' and pulls_response.get('message')['vox_task_id']:
        data = {
            "ticket_id" : pulls_response.get('message')['vox_task_id'],
            "status": 5,
            "email" : "admin@shikhartech.com",
            "password" : "admin@shikhartech.com123D"
        }
    
        response = requests.put(f'https://tickets.shikhartech.com/api/ticket/change-ticket-status', json=data)
        print(response.json())
    
    return True
