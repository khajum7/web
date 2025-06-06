provider "aws" {
  region = "us-west-1"
}

resource "aws_rds_cluster" "vivint_store" {
  cluster_identifier      = "vivint-store"
  engine                  = "aurora-mysql"
  engine_version          = "8.0.mysql_aurora.3.05.2"  # Upgrade target
  master_username         = "vivintStore"
  db_subnet_group_name    = "stg-db-sg"
  vpc_security_group_ids  = ["sg-0cc12cef7ededca5c"]
  storage_encrypted       = true
  kms_key_id              = "arn:aws:kms:us-west-1:174687093387:key/4754ff9f-6dde-4957-aac3-5462c64c1377"
  backup_retention_period = 1
  preferred_backup_window      = "07:30-08:00"
  preferred_maintenance_window = "fri:06:00-fri:06:30"
  deletion_protection     = false
  enabled_cloudwatch_logs_exports = ["audit", "error", "general", "slowquery"]

  apply_immediately       = true
}
