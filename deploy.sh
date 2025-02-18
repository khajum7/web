#!/bin/bash

echo "===== STARTING LAMBDA DEPLOYMENT ====="

LAMBDA_FUNCTION_NAME="Testing-CICD"
ZIP_FILE="lambda_$(date +%Y%m%d_%H%M%S).zip"
echo "Zip File Name: $ZIP_FILE"

# Define a sync directory (previously deployed files)
SYNC_DIR="/tmp/lambda_previous"

# Ensure the directory exists
mkdir -p "$SYNC_DIR"

# Get list of modified files
MODIFIED_FILES=$(rsync -av --dry-run --out-format="%n" . "$SYNC_DIR" | tail -n +2 | grep -v '/$')

# Check if any files were modified
if [ -z "$MODIFIED_FILES" ]; then
    echo "No files changed. Skipping deployment."
    exit 0
fi

echo "Modified files:"
echo "$MODIFIED_FILES"

# Copy only modified files to a temp directory
TMP_DIR="/tmp/lambda_update"
mkdir -p "$TMP_DIR"

echo "$MODIFIED_FILES" | while read FILE; do
    mkdir -p "$TMP_DIR/$(dirname "$FILE")"
    cp "$FILE" "$TMP_DIR/$FILE"
done

# Zip only the modified files
cd "$TMP_DIR" || exit 1
zip -r "../$ZIP_FILE" .

echo "Verifying Zip File..."
ls -lh "../$ZIP_FILE"

# Deploy to Lambda
cd ..
aws lambda update-function-code --function-name "$LAMBDA_FUNCTION_NAME" --zip-file "fileb://$ZIP_FILE"

echo "===== DEPLOYMENT COMPLETED ====="
