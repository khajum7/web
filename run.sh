#!/bin/bash

echo "Starting Lambda update process..."

# Enable full Git history
git fetch --unshallow 2> /dev/null || true

# Get current branch name
BRANCH=$(git rev-parse --abbrev-ref HEAD)

# Get all changed folders since last push
if [ "$(git rev-list --count origin/$BRANCH..HEAD)" -gt 0 ]; then
  CHANGED_FOLDERS=$(git diff --name-only origin/$BRANCH HEAD | cut -d'/' -f1 | sort | uniq)
else
  # For initial push or when we can't detect changes, process all folders
  CHANGED_FOLDERS=$(find . -mindepth 1 -maxdepth 1 -type d -printf '%f\n')
fi

echo "Folders to process: $CHANGED_FOLDERS"

# Process each folder
for FOLDER in $CHANGED_FOLDERS; do
  echo "Processing folder: $FOLDER"
  
  # Skip hidden directories
  if [[ "$FOLDER" == .* ]]; then
    echo "Skipping hidden directory: $FOLDER"
    continue
  fi

  # Verify folder exists
  if [ -d "$FOLDER" ]; then
    # Zip contents directly
    (cd "$FOLDER" && zip -r "../${FOLDER}.zip" .)
    
    # Update Lambda
    echo "Updating Lambda: $FOLDER"
    if aws lambda get-function --function-name "$FOLDER" > /dev/null 2>&1; then
      aws lambda update-function-code \
        --function-name "$FOLDER" \
        --zip-file "fileb://${FOLDER}.zip"
      echo "Successfully updated Lambda: $FOLDER"
    else
      echo "Warning: Lambda function $FOLDER not found - skipping"
    fi
  else
    echo "Skipping: $FOLDER is not a directory"
  fi
done

echo "Update process completed"
