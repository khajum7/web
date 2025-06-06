provider "aws" {
  region = "us-west-1"
}

resource "aws_db_cluster_snapshot" "snapshot_vivint_cluster" {
  db_cluster_identifier           = "vivint-store" # your Aurora cluster ID
  db_cluster_snapshot_identifier = "snapshot-vivint-cluster-${timestamp()}"
}
