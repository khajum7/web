provider "aws" {
  region = "us-west-1"
}

resource "aws_db_instance" "vivint_store_instance" {
  # Minimal placeholder for import reference
  skip_final_snapshot = true
}

resource "aws_db_snapshot" "snapshot_vivint" {
  db_instance_identifier = aws_db_instance.vivint_store_instance.id
  db_snapshot_identifier = "snapshot-vivint-final"
}
