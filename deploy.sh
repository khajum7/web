#!/bin/bash

echo "===== STARTING LAMBDA DEPLOYMENT ====="

LAMBDA_FUNCTION_NAME="Testing-CICD"
ZIP_FILE="lambda_$(date +%Y%m%d_%H%M%S).zip"
echo "Zip File Name: $ZIP_FILE"

# Define a sync directory (previously deployed files)
SYNC_DIR="/tmp/lambda_previous"

# Ensure the directory exists
mkdir -p "$SYNC_DIR"

# Find modified files compared to last sync and store in a list
MODIFIED_FILES=$(rsync -av --dry-run --out-format="%n" . "$SYNC_DIR" | tail -n +2)

if [ -z "$MODIFIED_FILES" ]; then
    echo "No files changed. Skipping deployment."
    exit 0
fi

# Copy only changed files
echo "$MODIFIED_FILES" | rsync -av --files-from=- . "$SYNC_DIR"

# Zip only the modified files
echo "$MODIFIED_FILES" | zip "$ZIP_FILE" -@

echo "Verifying Zip File..."
ls -lh "$ZIP_FILE"

# Deploy to Lambda
echo "Updating Lambda function: $LAMBDA_FUNCTION_NAME"
aws lambda update-function-code --function-name "$LAMBDA_FUNCTION_NAME" --zip-file "fileb://$ZIP_FILE"

echo "===== DEPLOYMENT COMPLETED ====="
