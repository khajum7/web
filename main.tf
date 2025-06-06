provider "aws" {
  region = "us-west-1"
}

resource "aws_db_instance" "vivint_store_instance" {
  instance_class         = "db.t3.medium"   # use the actual class of your existing RDS instance
  allocated_storage      = 1               # dummy value; not used during import
  engine                 = "aurora-mysql"         # placeholder; won't overwrite real settings on import
  skip_final_snapshot    = true
}

resource "aws_db_snapshot" "snapshot_vivint" {
  db_instance_identifier = aws_db_instance.vivint_store_instance.id
  db_snapshot_identifier = "snapshot-vivint-final"
}
