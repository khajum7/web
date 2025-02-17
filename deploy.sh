#!/bin/bash

echo "===== STARTING LAMBDA DEPLOYMENT ====="

# Variables
LAMBDA_FUNCTION_NAME="Testing-CICD"

# Generate a dynamic zip file name based on timestamp
ZIP_FILE="lambda_$(date +%Y%m%d_%H%M%S).zip"

echo "Zip File Name: $ZIP_FILE"

# Get list of files in the build
echo "Listing Files in the Build Environment:"
ls -la

# Zip all files (since Git history is missing in CodeBuild)
echo "Zipping all files (since Git is not available in CodeBuild)..."
zip -r "$ZIP_FILE" . 

echo "Verifying Zip File..."
ls -lh "$ZIP_FILE"

# Deploy to Lambda
echo "Updating Lambda function: $LAMBDA_FUNCTION_NAME"
aws lambda update-function-code --function-name "$LAMBDA_FUNCTION_NAME" --zip-file "fileb://$ZIP_FILE"

echo "===== DEPLOYMENT COMPLETED ====="
