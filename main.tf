provider "aws" {
  region = "us-west-1"
}

resource "aws_rds_cluster" "vivint_store" {
  cluster_identifier      = "vivint-store"
  engine                  = "aurora-mysql"
  engine_version          = "8.0.mysql_aurora.3.08.2"  # Upgrade target
  master_username         = "vivintStore"
  db_subnet_group_name    = "stg-db-sg"
  vpc_security_group_ids  = ["sg-0cc12cef7ededca5c"]
  storage_encrypted       = true
  kms_key_id              = "arn:aws:kms:us-west-1:174687093387:key/4754ff9f-6dde-4957-aac3-5462c64c1377"
  backup_retention_period = 1
  preferred_backup_window = "06:34-07:04"
  preferred_maintenance_window = "fri:06:57-fri:07:27"
  deletion_protection     = false
  enabled_cloudwatch_logs_exports = ["audit", "error", "general", "slowquery"]

  apply_immediately       = true
  parameter_group_name    = "voxmg8-pg"
}

resource "aws_rds_cluster_instance" "vivint_store_instance_1" {
  identifier              = "vivint-store-instance-1"
  cluster_identifier      = "vivint-store"
  instance_class          = "db.t3.medium"
  engine                  = "aurora-mysql"
  db_subnet_group_name    = "stg-db-sg"
  publicly_accessible     = false
  auto_minor_version_upgrade = true
  monitoring_interval     = 60
  monitoring_role_arn     = "arn:aws:iam::174687093387:role/rds-monitoring-role"
  performance_insights_enabled     = false
  performance_insights_kms_key_id = "arn:aws:kms:us-west-1:174687093387:key/4754ff9f-6dde-4957-aac3-5462c64c1377"
  apply_immediately       = true
}
