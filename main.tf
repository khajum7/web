provider "aws" {
  region = "us-west-1"
}

resource "aws_db_cluster_snapshot" "snapshot_vivint_cluster" {
  db_cluster_identifier           = "vivint-store"
  db_cluster_snapshot_identifier = "snapshot-vivint-cluster-${replace(timestamp(), "/[^a-zA-Z0-9]/", "-")}"
}
