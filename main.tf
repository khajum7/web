provider "aws" {
  region = "us-west-1"
}

# --------------------------
# Reference Existing Security Group
# --------------------------
data "aws_security_group" "st_server_sg" {
  name   = "ST-Server-SG"
  vpc_id = "vpc-01add8eecbcf22150"
}

# --------------------------
# Reference Existing IAM Role and Instance Profile
# --------------------------
data "aws_iam_role" "st_ec2_role" {
  name = "ST-Ec2-Roles"
}

data "aws_iam_instance_profile" "st_ec2_instance_profile" {
  name = "ST-Ec2-Role-Profile"
}

# --------------------------
# EC2 Instance (Ubuntu 24.04 LTS)
# --------------------------
resource "aws_instance" "ubuntu_server" {
  ami                         = "ami-00271c85bf8a52b84" # Ubuntu 24.04 LTS (x86)
  instance_type               = "t3.micro"
  subnet_id                   = "subnet-02e185dde50e906dc"
  key_name                    = "Dev-server"
  associate_public_ip_address = true
  vpc_security_group_ids      = [data.aws_security_group.st_server_sg.id]
  iam_instance_profile        = data.aws_iam_instance_profile.st_ec2_instance_profile.name

  root_block_device {
    volume_size = 20
    volume_type = "gp3"
  }

  tags = {
    Name = "ST-Ubuntu-Server"
  }
}

# --------------------------
# Outputs
# --------------------------
output "instance_public_ip" {
  value = aws_instance.ubuntu_server.public_ip
}

output "security_group_id" {
  value = data.aws_security_group.st_server_sg.id
}

output "iam_role_name" {
  value = data.aws_iam_role.st_ec2_role.name
}
