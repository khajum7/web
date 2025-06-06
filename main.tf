provider "aws" {
  region = "us-west-1"
}

resource "aws_db_cluster_snapshot" "vivint-snapshot" {
  db_cluster_identifier          = "vivint-store"
  db_cluster_snapshot_identifier = "snapshot-vivint-terraform"
}
