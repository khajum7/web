provider "aws" {
  region = "us-west-1"
}

resource "aws_rds_cluster_snapshot" "snapshot_vivint_cluster" {
  db_cluster_identifier           = "vivint-store"
  db_cluster_snapshot_identifier = "snapshot-vivint-cluster-final"
}
