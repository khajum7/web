#!/bin/bash

set -e

REGION="us-west-1"
TMP_HASH_FILE=".lambda_code_hashes"

touch "$TMP_HASH_FILE"

for dir in */; do
  FOLDER_NAME="${dir%/}"

  echo "Checking: $FOLDER_NAME"

  # Calculate current folder content hash (based on actual files, not ZIP)
  CONTENT_HASH=$(find "$FOLDER_NAME" -type f -exec sha256sum {} \; | sort | sha256sum | awk '{print $1}')

  # Get previous hash if exists
  LAST_HASH=$(grep "^$FOLDER_NAME:" "$TMP_HASH_FILE" | cut -d: -f2)

  if [ "$CONTENT_HASH" != "$LAST_HASH" ]; then
    echo "🔄 Detected file change. Preparing to update: $FOLDER_NAME"

    # Zip only if content changed
    ZIP_FILE="${FOLDER_NAME}.zip"
    cd "$FOLDER_NAME"
    zip -r "../$ZIP_FILE" . > /dev/null
    cd ..

    if aws lambda update-function-code \
  --region "$REGION" \
  --function-name "$FOLDER_NAME" \
  --zip-file "fileb://$ZIP_FILE"; then

  # Update stored hash only if upload succeeded
  grep -v "^$FOLDER_NAME:" "$TMP_HASH_FILE" > "$TMP_HASH_FILE.tmp"
  echo "$FOLDER_NAME:$CONTENT_HASH" >> "$TMP_HASH_FILE.tmp"
  mv "$TMP_HASH_FILE.tmp" "$TMP_HASH_FILE"

  # Cleanup
  rm -f "$ZIP_FILE"

else
  echo "❌ Failed to update Lambda: $FOLDER_NAME"
  exit 1
fi
