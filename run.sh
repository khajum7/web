#!/bin/bash

echo "Starting full Lambda update process..."

# Process all top-level directories
find . -mindepth 1 -maxdepth 1 -type d -not -path '*/.*' -printf '%f\n' | while read FOLDER; do
  echo "Processing folder: $FOLDER"
  
  # Zip contents
  (cd "$FOLDER" && zip -r "../${FOLDER}.zip" .)
  
  # Update Lambda if exists
  if aws lambda get-function --function-name "$FOLDER" > /dev/null 2>&1; then
    aws lambda update-function-code \
      --function-name "$FOLDER" \
      --zip-file "fileb://${FOLDER}.zip"
    echo "Updated Lambda: $FOLDER"
  else
    echo "Lambda $FOLDER not found - skipping"
  fi
done

echo "Update process completed"
