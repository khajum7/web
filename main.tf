provider "aws" {
  region = "us-west-1"   # Use the actual region of your RDS
}

resource "aws_db_instance" "vivint_store_instance_1" {
  identifier              = "vivint-store-instance-1"
  engine                  = "aurora-mysql"
  engine_version          = "8.0.mysql_aurora.3.08.2"    # New version to upgrade to
  instance_class          = "db.t3.medium"
  publicly_accessible     = false
  storage_encrypted       = true
  backup_retention_period = 1
  db_subnet_group_name    = "stg-db-sg"
  vpc_security_group_ids  = ["sg-0cc12cef7ededca5c"]
  multi_az                = false
  apply_immediately       = true        # Applies upgrade immediately
  auto_minor_version_upgrade = true

  # Optional: monitoring related settings (from your details)
  monitoring_interval     = 60
  monitoring_role_arn     = "arn:aws:iam::174687093387:role/rds-monitoring-role"

  # Parameter group - match existing if needed
  parameter_group_name    = "voxmg8-pg"

  # Maintenance window
  maintenance_window      = "fri:06:57-fri:07:27"

  # Tags (empty for now, add if needed)
  tags = {}
}
