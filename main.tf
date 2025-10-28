provider "aws" {
  region = "us-west-1"
}

# --------------------------
# Security Group
# --------------------------
resource "aws_security_group" "st_server_sg" {
  name        = "ST-Server-SG"
  description = "Allow HTTP, HTTPS, and SSH"
  vpc_id      = "vpc-01add8eecbcf22150"

  ingress {
    description = "HTTP"
    from_port   = 80
    to_port     = 80
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    description = "HTTPS"
    from_port   = 443
    to_port     = 443
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    description = "SSH"
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  egress {
    description = "All outbound"
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = {
    Name = "ST-Server-SG"
  }
}

# --------------------------
# IAM Role and Instance Profile
# --------------------------
resource "aws_iam_role" "st_ec2_role" {
  name = "ST-Ec2-Roles"

  assume_role_policy = jsonencode({
    Version = "2012-10-17"
    Statement = [{
      Action = "sts:AssumeRole"
      Effect = "Allow"
      Principal = {
        Service = "ec2.amazonaws.com"
      }
    }]
  })
}

resource "aws_iam_role_policy_attachment" "ec2_basic" {
  role       = aws_iam_role.st_ec2_role.name
  policy_arn = "arn:aws:iam::aws:policy/AmazonSSMManagedInstanceCore"
}

resource "aws_iam_instance_profile" "st_ec2_instance_profile" {
  name = "ST-Ec2-Role-Profile"
  role = aws_iam_role.st_ec2_role.name
}

# --------------------------
# EC2 Instance (Ubuntu 24.04 LTS)
# --------------------------
resource "aws_instance" "ubuntu_server" {
  ami                         = "ami-00271c85bf8a52b84" # Ubuntu 24.04 LTS (x86) 
  instance_type               = "t3.large"
  subnet_id                   = "subnet-02e185dde50e906dc"
  key_name                    = "Dev-server"
  associate_public_ip_address = true
  vpc_security_group_ids      = [aws_security_group.st_server_sg.id]
  iam_instance_profile        = aws_iam_instance_profile.st_ec2_instance_profile.name

  root_block_device {
    volume_size = 20
    volume_type = "gp3"
  }

  tags = {
    Name = "ST-Ubuntu-Server"
  }
}

output "instance_public_ip" {
  value = aws_instance.ubuntu_server.public_ip
}

output "security_group_id" {
  value = aws_security_group.st_server_sg.id
}

output "iam_role_name" {
  value = aws_iam_role.st_ec2_role.name
}
