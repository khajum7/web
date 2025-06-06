resource "aws_db_instance" "vivint-store-instance-1" {
  allocated_storage       = 1
  engine                  = "aurora-mysql"
  engine_version          = "8.0.mysql_aurora.3.08.2"
  instance_class          = "db.t3.medium"
  db_name                 = "vivintStore"
  password                = "e0;M1r~=<Pm4==F}scTuP[j?)*j4qPmn"
  username                = "vivintStore"

  maintenance_window      = "Fri:09:00-Fri:09:30"
  backup_retention_period = 0
  parameter_group_name    = "voxmg8-pg"
}

resource "aws_db_snapshot" "Snapshot-vivnt" {
  db_instance_identifier       = aws_db_instance.vivint-store-instance-1.identifier
  db_snapshot_identifier       = "Backup-vivint"
}
