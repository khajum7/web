#!/bin/bash

# Exit on any failure
set -e

# Ensure required environment variables are set
if [[ -z "$STATUS" || -z "$DESC" || -z "$CODEBUILD_RESOLVED_SOURCE_VERSION" || -z "$REPO_OWNER" || -z "$REPO_NAME" || -z "$GITHUB_TOKEN" ]]; then
  echo "❌ Missing one or more required environment variables."
  exit 1
fi

echo "📢 Reporting status to GitHub..."
echo "STATE: $STATUS"
echo "DESC: $DESC"
echo "SHA: $CODEBUILD_RESOLVED_SOURCE_VERSION"
echo "REPO: $REPO_OWNER/$REPO_NAME"

# Create status payload
cat > status_payload.json <<EOF
{
  "state": "$STATUS",
  "description": "$DESC",
  "context": "CodeBuild PR Syntax Checker"
}
EOF

cat status_payload.json

# Send status to GitHub
curl -s -o /dev/null -w "%{http_code}" -X POST \
  -H "Authorization: token $GITHUB_TOKEN" \
  -H "Accept: application/vnd.github+json" \
  -H "Content-Type: application/json" \
  --data @status_payload.json \
  "https://api.github.com/repos/$REPO_OWNER/$REPO_NAME/statuses/$CODEBUILD_RESOLVED_SOURCE_VERSION"
