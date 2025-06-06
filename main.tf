provider "aws" {
  region = "us-west-1"
}


resource "aws_db_snapshot" "Snapshot-vivnt" {
  db_instance_identifier       = aws_db_instance.vivint-store-instance-1.identifier
  db_snapshot_identifier       = "Backup-vivint"
}
