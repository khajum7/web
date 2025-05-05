#!/bin/bash

set -e

REGION="us-west-1"
FOLDER_NAME="$1"

if [ -z "$FOLDER_NAME" ]; then
  echo "Usage: $0 <lambda_folder_name>"
  exit 1
fi

ZIP_FILE="${FOLDER_NAME}.zip"

# Create ZIP
cd "$FOLDER_NAME"
zip -r "../$ZIP_FILE" . > /dev/null
cd ..

# Check if Lambda function exists
if aws lambda get-function --region "$REGION" --function-name "$FOLDER_NAME" > /tmp/lambda_info.json 2>/dev/null; then
  REMOTE_HASH=$(jq -r .Configuration.CodeSha256 /tmp/lambda_info.json)
  LOCAL_HASH=$(openssl dgst -sha256 -binary "$ZIP_FILE" | openssl base64)

  if [ "$REMOTE_HASH" != "$LOCAL_HASH" ]; then
    echo "Code changed. Updating Lambda: $FOLDER_NAME"
    aws lambda update-function-code \
      --region "$REGION" \
      --function-name "$FOLDER_NAME" \
      --zip-file "fileb://$ZIP_FILE"
  else
    echo "No code change for $FOLDER_NAME. Skipping update."
  fi
else
  echo "Lambda function $FOLDER_NAME not found in region $REGION."
fi
