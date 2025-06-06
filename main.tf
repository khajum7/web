provider "aws" {
  region = "us-west-1"
}

resource "aws_db_cluster_snapshot" "voxecommerce-cluster-snapshot" {
  db_cluster_identifier          = "voxecommerce-cluster"
  db_cluster_snapshot_identifier = "voxecommerce-cluster-terraform"
}
