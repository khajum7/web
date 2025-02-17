#!/bin/bash

echo "===== STARTING LAMBDA DEPLOYMENT ====="

# Variables
LAMBDA_FUNCTION_NAME="Testing-CICD"
REPO_NAME=$(basename `git rev-parse --show-toplevel`)
COMMIT_ID=$(git rev-parse --short HEAD)
ZIP_FILE="${REPO_NAME}_${COMMIT_ID}.zip"

echo "Repository Name: $REPO_NAME"
echo "Commit ID: $COMMIT_ID"
echo "Zip File Name: $ZIP_FILE"

echo "Getting modified files..."
MODIFIED_FILES=$(git diff --name-only HEAD~1 HEAD)

if [ -z "$MODIFIED_FILES" ]; then
    echo "No changes detected. Skipping deployment."
    exit 0
fi

echo "Modified Files:"
echo "$MODIFIED_FILES"

echo "Zipping only modified files..."
zip -r "$ZIP_FILE" $MODIFIED_FILES

echo "Verifying Zip File..."
ls -lh "$ZIP_FILE"

echo "Updating Lambda function: $LAMBDA_FUNCTION_NAME"
aws lambda update-function-code --function-name "$LAMBDA_FUNCTION_NAME" --zip-file "fileb://$ZIP_FILE"

echo "===== DEPLOYMENT COMPLETED ====="
