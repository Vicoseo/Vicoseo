#!/bin/bash
set -e

DOMAIN="$1"
if [ -z "$DOMAIN" ]; then
    echo '{"success":false,"message":"Domain parametresi gerekli"}'
    exit 1
fi

# Strip www prefix for base domain
BASE_DOMAIN=$(echo "$DOMAIN" | sed 's/^www\.//')
CONF_NAME=$(echo "$BASE_DOMAIN" | tr '.' '-')

# Check if already provisioned
if [ -f "/etc/nginx/sites-available/$CONF_NAME" ]; then
    echo "{\"success\":true,\"message\":\"$BASE_DOMAIN zaten yapilandirilmis\",\"already_exists\":true}"
    exit 0
fi

# Step 1: Ensure certbot webroot dir exists
mkdir -p /var/www/certbot

# Step 2: Get SSL certificate
certbot certonly --nginx \
    -d "$BASE_DOMAIN" -d "www.$BASE_DOMAIN" \
    --non-interactive --agree-tos --email "admin@$BASE_DOMAIN" 2>&1

if [ ! -f "/etc/letsencrypt/live/$BASE_DOMAIN/fullchain.pem" ]; then
    echo "{\"success\":false,\"message\":\"SSL sertifikasi alinamadi. DNS ayarlarini kontrol edin.\"}"
    exit 1
fi

# Step 3: Generate Nginx config
SESSION_NAME=$(echo "$BASE_DOMAIN" | tr '.' '_' | tr '-' '_')

cat > "/etc/nginx/sites-available/$CONF_NAME" <<NGINXEOF
# www -> non-www redirect
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name www.${BASE_DOMAIN};

    ssl_certificate /etc/letsencrypt/live/${BASE_DOMAIN}/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/${BASE_DOMAIN}/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;

    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;

    return 301 https://${BASE_DOMAIN}\$request_uri;
}

# ${BASE_DOMAIN} - Tenant Site
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name ${BASE_DOMAIN};

    ssl_certificate /etc/letsencrypt/live/${BASE_DOMAIN}/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/${BASE_DOMAIN}/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_prefer_server_ciphers off;
    ssl_session_cache shared:${SESSION_NAME}SSL:10m;
    ssl_session_timeout 1d;

    root /var/www/multi-tenant-cms/backend/public;
    index index.php;
    charset utf-8;
    client_max_body_size 20M;

    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript image/svg+xml;
    gzip_vary on;
    gzip_min_length 1024;

    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Permissions-Policy "camera=(), microphone=(), geolocation=()" always;

    location /api {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location /sanctum {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php\$ {
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_buffer_size 16k;
        fastcgi_buffers 16 16k;
        fastcgi_read_timeout 60s;
        fastcgi_param HTTP_HOST \$host;
        fastcgi_param SERVER_NAME \$host;
    }

    location /storage {
        alias /var/www/multi-tenant-cms/backend/storage/app/public;
        expires 30d;
        add_header Cache-Control "public, immutable";
    }

    location /_next/static {
        proxy_pass http://nextjs_upstream;
        proxy_http_version 1.1;
        proxy_set_header Connection "";
        expires 365d;
        add_header Cache-Control "public, immutable";
    }

    location / {
        proxy_pass http://nextjs_upstream;
        proxy_http_version 1.1;
        proxy_set_header Upgrade \$http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host \$host;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto \$scheme;
        proxy_set_header X-Forwarded-Host \$host;
        proxy_cache_bypass \$http_upgrade;
        proxy_buffer_size 16k;
        proxy_buffers 16 16k;
        proxy_read_timeout 60s;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}

# HTTP -> HTTPS redirect
server {
    listen 80;
    listen [::]:80;
    server_name ${BASE_DOMAIN} www.${BASE_DOMAIN};

    location /.well-known/acme-challenge/ {
        root /var/www/certbot;
        allow all;
    }

    location / {
        return 301 https://${BASE_DOMAIN}\$request_uri;
    }
}
NGINXEOF

# Step 4: Enable and reload
ln -sf "/etc/nginx/sites-available/$CONF_NAME" "/etc/nginx/sites-enabled/$CONF_NAME"
nginx -t 2>&1
if [ $? -ne 0 ]; then
    rm -f "/etc/nginx/sites-enabled/$CONF_NAME"
    echo "{\"success\":false,\"message\":\"Nginx yapilandirma hatasi\"}"
    exit 1
fi

systemctl reload nginx

echo "{\"success\":true,\"message\":\"$BASE_DOMAIN basariyla yapilandirildi. SSL sertifikasi ve Nginx ayarlari aktif.\"}"
