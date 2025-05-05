#!/bin/bash

set -e  # Exit on any command failure
set -x  # Print each command before executing

echo "Starting full Lambda update process..."

REGION="us-west-1"  # Set your region

find . -mindepth 1 -maxdepth 1 -type d -not -path '*/.*' -printf '%f\n' | while read FOLDER; do
  echo "-----------------------------"
  echo "Processing folder: $FOLDER"
  
  ZIP_FILE="${FOLDER}.zip"

  # Remove existing zip if any
  rm -f "$ZIP_FILE"

  # Zip contents
  (cd "$FOLDER" && zip -r "../${ZIP_FILE}" .)

  # Check if Lambda function exists
  echo "Checking if Lambda function '$FOLDER' exists..."
  if aws lambda get-function --region "$REGION" --function-name "$FOLDER" > /tmp/lambda_info.json 2>/dev/null; then
    echo "Lambda found. Checking for code changes..."

    # Get current remote Lambda code hash
    REMOTE_HASH=$(jq -r '.Configuration.CodeSha256' /tmp/lambda_info.json)

    # Get local zip file's base64-encoded SHA-256 hash
    LOCAL_HASH=$(openssl dgst -sha256 -binary "$ZIP_FILE" | openssl base64)

    if [ "$REMOTE_HASH" == "$LOCAL_HASH" ]; then
      echo "⚠️  No changes detected for '$FOLDER'. Skipping update."
    else
      echo "🔄 Changes detected. Updating '$FOLDER'..."
      aws lambda update-function-code \
        --region "$REGION" \
        --function-name "$FOLDER" \
        --zip-file "fileb://${ZIP_FILE}"
      echo "✅ Updated Lambda: $FOLDER"
    fi
  else
    echo "❌ Lambda '$FOLDER' not found in region '$REGION' - skipping"
  fi
done

echo "Update process completed ✅"
