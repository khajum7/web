#!/bin/bash

echo "===== STARTING LAMBDA DEPLOYMENT ====="

# Variables
LAMBDA_FUNCTION_NAME="Testing-CICD"

# Get repository name from Git
REPO_NAME=$(basename `git rev-parse --show-toplevel`)

# Get commit ID
COMMIT_ID=$(git rev-parse --short HEAD)

# Create dynamic zip filename
ZIP_FILE="${REPO_NAME}_${COMMIT_ID}.zip"

echo "Repository Name: $REPO_NAME"
echo "Commit ID: $COMMIT_ID"
echo "Zip File Name: $ZIP_FILE"

echo "Current Directory: $(pwd)"
echo "Listing Files:"
ls -la

echo "Zipping Lambda function..."
zip -r "$ZIP_FILE" . 

echo "Verifying Zip File..."
ls -lh "$ZIP_FILE"

echo "Updating Lambda function: $LAMBDA_FUNCTION_NAME"
aws lambda update-function-code --function-name "$LAMBDA_FUNCTION_NAME" --zip-file "fileb://$ZIP_FILE"

echo "===== DEPLOYMENT COMPLETED ====="
