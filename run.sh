#!/bin/bash

set -e  # Exit on any command failure
set -x  # Print each command before executing

echo "Starting full Lambda update process..."

# Set your region explicitly
REGION="us-west-1"  # change this to your actual region

# Process all top-level directories
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
  if aws lambda get-function --region "$REGION" --function-name "$FOLDER"; then
    echo "Lambda found. Updating '$FOLDER'..."
    aws lambda update-function-code \
      --region "$REGION" \
      --function-name "$FOLDER" \
      --zip-file "fileb://${ZIP_FILE}"
    echo "✅ Updated Lambda: $FOLDER"
  else
    echo "❌ Lambda '$FOLDER' not found in region '$REGION' - skipping"
  fi
done

echo "Update process completed ✅"
