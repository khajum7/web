#!/bin/bash

echo "===== STARTING LAMBDA DEPLOYMENT ====="

LAMBDA_FUNCTION_NAME="Testing-CICD"
ZIP_FILE="lambda_$(date +%Y%m%d_%H%M%S).zip"
echo "Zip File Name: $ZIP_FILE"

# Define a sync directory (previously deployed files)
SYNC_DIR="/tmp/lambda_previous"

# Ensure the directory exists
mkdir -p "$SYNC_DIR"

# Use rsync to detect changes and copy updated files to a temp directory
rsync -av --update --delete . "$SYNC_DIR"

# Zip only the updated files
cd "$SYNC_DIR" && zip -r "../$ZIP_FILE" .

echo "Verifying Zip File..."
ls -lh "../$ZIP_FILE"

# Deploy to Lambda
echo "Updating Lambda function: $LAMBDA_FUNCTION_NAME"
aws lambda update-function-code --function-name "$LAMBDA_FUNCTION_NAME" --zip-file "fileb://../$ZIP_FILE"

echo "===== DEPLOYMENT COMPLETED ====="
