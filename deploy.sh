#!/bin/bash

echo "===== STARTING LAMBDA DEPLOYMENT ====="

LAMBDA_FUNCTION_NAME="Testing-CICD"
ZIP_FILE="lambda_$(date +%Y%m%d_%H%M%S).zip"
echo "Zip File Name: $ZIP_FILE"

# Define sync directory (previously deployed files)
SYNC_DIR="/tmp/lambda_previous"

# Ensure the directory exists
mkdir -p "$SYNC_DIR"

# Get list of modified files compared to last sync
MODIFIED_FILES=$(rsync -rc --out-format="%n" --dry-run . "$SYNC_DIR" | grep -v '/$')

# Check if any files were modified
if [ -z "$MODIFIED_FILES" ]; then
    echo "No files changed. Skipping deployment."
    exit 0
fi

echo "Modified files:"
echo "$MODIFIED_FILES"

# Create a temporary directory for updated files
TMP_DIR="/tmp/lambda_update"
rm -rf "$TMP_DIR"
mkdir -p "$TMP_DIR"

# Copy only updated files to the temp directory
while read -r FILE; do
    if [ -f "$FILE" ]; then
        mkdir -p "$TMP_DIR/$(dirname "$FILE")"
        cp "$FILE" "$TMP_DIR/$FILE"
    fi
done <<< "$MODIFIED_FILES"

# Zip only modified files
cd "$TMP_DIR" || exit 1
zip -r "../$ZIP_FILE" .

echo "Verifying Zip File..."
ls -lh "../$ZIP_FILE"

# Deploy to Lambda
cd ..
aws lambda update-function-code --function-name "$LAMBDA_FUNCTION_NAME" --zip-file "fileb://$ZIP_FILE"

# Sync the latest version to prevent unnecessary re-deployments
rsync -rc . "$SYNC_DIR"

echo "===== DEPLOYMENT COMPLETED ====="
