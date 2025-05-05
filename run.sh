#!/bin/bash

set -e

REGION="us-west-1"

for dir in */; do
  FOLDER_NAME="${dir%/}"
  ZIP_FILE="${FOLDER_NAME}.zip"

  echo "Checking: $FOLDER_NAME"

  # Zip the Lambda function folder
  cd "$FOLDER_NAME"
  zip -r "../$ZIP_FILE" . > /dev/null
  cd ..

  # Get remote Lambda code hash
  if aws lambda get-function --region "$REGION" --function-name "$FOLDER_NAME" > /tmp/lambda_info.json 2>/dev/null; then
    REMOTE_HASH=$(jq -r .Configuration.CodeSha256 /tmp/lambda_info.json)
    LOCAL_HASH=$(openssl dgst -sha256 -binary "$ZIP_FILE" | openssl base64)

    if [ "$REMOTE_HASH" != "$LOCAL_HASH" ]; then
      echo "🔄 Code changed. Updating Lambda: $FOLDER_NAME"
      aws lambda update-function-code \
        --region "$REGION" \
        --function-name "$FOLDER_NAME" \
        --zip-file "fileb://$ZIP_FILE"
    else
      echo "✅ No change for $FOLDER_NAME. Skipping."
    fi
  else
    echo "⚠️ Lambda $FOLDER_NAME not found in $REGION."
  fi

  # Cleanup
  rm -f "$ZIP_FILE"
done
