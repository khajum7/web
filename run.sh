#!/bin/bash

set -e  # Exit on any command failure
set -x  # Print each command before executing

echo "Starting full Lambda update process..."

REGION="us-west-1"  # Change this to your actual region
HASH_DIR=".lambda_hashes"
mkdir -p "$HASH_DIR"

# Function to compute hash of a folder
compute_hash() {
  find "$1" -type f -exec sha256sum {} \; | sort | sha256sum | awk '{print $1}'
}

# Process each top-level folder
find . -mindepth 1 -maxdepth 1 -type d -not -path '*/.*' -printf '%f\n' | while read FOLDER; do
  echo "-----------------------------"
  echo "Checking folder: $FOLDER"
  
  CURRENT_HASH=$(compute_hash "$FOLDER")
  HASH_FILE="${HASH_DIR}/${FOLDER}.hash"

  if [[ -f "$HASH_FILE" ]]; then
    PREVIOUS_HASH=$(cat "$HASH_FILE")
  else
    PREVIOUS_HASH=""
  fi

  if [[ "$CURRENT_HASH" == "$PREVIOUS_HASH" ]]; then
    echo "✅ No changes detected in $FOLDER. Skipping update."
    continue
  fi

  echo "🆕 Changes detected in $FOLDER. Proceeding with update..."

  ZIP_FILE="${FOLDER}.zip"
  rm -f "$ZIP_FILE"
  (cd "$FOLDER" && zip -r "../${ZIP_FILE}" .)

  echo "Checking if Lambda function '$FOLDER' exists..."
  if aws lambda get-function --region "$REGION" --function-name "$FOLDER" > /dev/null 2>&1; then
    echo "Updating Lambda function '$FOLDER'..."
    aws lambda update-function-code \
      --region "$REGION" \
      --function-name "$FOLDER" \
      --zip-file "fileb://${ZIP_FILE}"
    echo "$CURRENT_HASH" > "$HASH_FILE"
    echo "✅ Lambda '$FOLDER' updated and hash saved."
  else
    echo "❌ Lambda function '$FOLDER' not found. Skipping."
  fi

done

echo "All done ✅"
