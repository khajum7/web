#!/bin/bash

set -e

echo "Starting application_start hook..."

# Create systemd service for backend
echo "Creating systemd service for backend..."
cat > /etc/systemd/system/hoppscotch-backend.service << 'EOF'
[Unit]
Description=Hoppscotch Backend
After=network.target postgresql.service redis-server.service

[Service]
WorkingDirectory=/var/www/html/hoppscotch/packages/hoppscotch-backend
Environment=DATABASE_URL=postgresql://Postman-user:PqwriuofhadgfiaduhUIFvb%23iudf234@13.52.23.186:5432/hoppdb?schema=public
Environment=PRODUCTION=true
Environment=PORT=3170
Environment=TRUST_PROXY=true
Environment=WHITELISTED_ORIGINS=http://man.shikhartech.com,https://man.shikhartech.com
ExecStart=/usr/bin/node dist/main.js
Restart=on-failure
User=ubuntu
Group=ubuntu

[Install]
WantedBy=multi-user.target
EOF

# Create Apache virtual host
echo "Creating Apache virtual host..."
cat > /etc/apache2/sites-available/hoppscotch.conf << 'EOF'
<VirtualHost *:80>
    ServerName man.shikhartech.com
    DocumentRoot /var/www/html/hoppscotch/packages/hoppscotch-selfhost-web/dist

    <Directory /var/www/html/hoppscotch/packages/hoppscotch-selfhost-web/dist>
        Options FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # SPA fallback to index.html
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^ /index.html [L]

    # Proxy API calls to backend
    ProxyPreserveHost On
    ProxyPass /backend/ http://127.0.0.1:3170/
    ProxyPassReverse /backend/ http://127.0.0.1:3170/
</VirtualHost>
EOF

# Enable Apache modules
echo "Enabling Apache modules..."
a2enmod proxy proxy_http rewrite headers

# Enable site and disable default
echo "Configuring Apache sites..."
a2ensite hoppscotch.conf
a2dissite 000-default.conf

# Reload systemd and start services
echo "Starting services..."
systemctl daemon-reload
systemctl enable hoppscotch-backend
systemctl start hoppscotch-backend
systemctl restart apache2

# Wait a moment for services to start
sleep 5

# Check service status
echo "Checking service status..."
systemctl status hoppscotch-backend --no-pager -l
systemctl status apache2 --no-pager -l

# Test if services are responding
echo "Testing service endpoints..."
curl -f http://13.52.23.186:3170/ || echo "Backend not responding on port 3170"
curl -f http://13.52.23.186:80/ || echo "Apache not responding on port 80"

echo "Application start completed successfully"
echo "Your app should be available at: http://man.shikhartech.com"
