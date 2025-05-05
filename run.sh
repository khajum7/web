#!/bin/bash

set -e  # Exit on any command failure
set -x  # Print each command before executing

echo "Starting filtered Lambda update process..."

REGION="us-west-1"
HASH_TRACK_FILE=".lambda_hashes"

# Ensure the hash record file exists
touch "$HASH_TRACK_FILE"

# Loop through top-level directories
find . -mindepth 1 -maxdepth 1 -type d -not -path '*/.*' -printf '%f\n' | while read FOLDER; do
  echo "-----------------------------"
  echo "Processing folder: $FOLDER"
  ZIP_FILE="${FOLDER}.zip"

  # Calculate folder hash (excluding .zip and hidden files)
  CONTENT_HASH=$(find "$FOLDER" -type f ! -name '*.zip' ! -path '*/.*' -exec sha256sum {} + | sort | sha256sum | awk '{print $1}')
  LAST_HASH=$(grep "^${FOLDER}:" "$HASH_TRACK_FILE" | cut -d ':' -f2)

  if [ "$CONTENT_HASH" == "$LAST_HASH" ]; then
    echo "✅ No code change detected in '$FOLDER'. Skipping zip and update."
    continue
  fi

  echo "🔄 Detected code change in '$FOLDER'. Zipping and updating Lambda..."
  rm -f "$ZIP_FILE"
  (cd "$FOLDER" && zip -r "../${ZIP_FILE}" . > /dev/null)

  if aws lambda get-function --region "$REGION" --function-name "$FOLDER" > /dev/null 2>&1; then
    aws lambda update-function-code \
      --region "$REGION" \
      --function-name "$FOLDER" \
      --zip-file "fileb://${ZIP_FILE}"

    echo "✅ Updated Lambda: $FOLDER"
    # Update stored hash
    grep -v "^${FOLDER}:" "$HASH_TRACK_FILE" > "${HASH_TRACK_FILE}.tmp"
    echo "${FOLDER}:${CONTENT_HASH}" >> "${HASH_TRACK_FILE}.tmp"
    mv "${HASH_TRACK_FILE}.tmp" "$HASH_TRACK_FILE"
  else
    echo "❌ Lambda '$FOLDER' not found. Skipping..."
  fi

  rm -f "$ZIP_FILE"
done

echo "Lambda update process complete ✅"
