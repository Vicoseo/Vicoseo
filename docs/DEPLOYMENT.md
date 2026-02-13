# Multi-Tenant CMS - Production Deployment Guide

This guide covers deploying the multi-tenant CMS system to a production Linux server (Ubuntu 22.04/24.04 LTS recommended).

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Server Architecture](#server-architecture)
3. [Server Setup](#1-server-setup)
4. [MySQL Setup](#2-mysql-setup)
5. [Laravel Backend Deployment](#3-laravel-backend-deployment)
6. [Next.js Frontend Deployment](#4-nextjs-frontend-deployment)
7. [Nginx Configuration](#5-nginx-configuration)
8. [Redis Configuration](#6-redis-configuration)
9. [Queue Worker (Supervisor)](#7-queue-worker-supervisor)
10. [Performance Optimization](#8-performance-optimization)
11. [Scaling Considerations](#9-scaling-considerations)
12. [Monitoring and Maintenance](#10-monitoring-and-maintenance)
13. [Adding a New Site](#11-adding-a-new-site)
14. [Artisan Commands Reference](#12-artisan-commands-reference)
15. [Troubleshooting](#troubleshooting)

---

## Prerequisites

| Software   | Minimum Version | Purpose                        |
|------------|-----------------|--------------------------------|
| PHP        | 8.2+            | Laravel backend runtime        |
| MySQL      | 8.0+            | Landlord + tenant databases    |
| Redis      | 6.0+            | Cache, sessions, queue broker  |
| Node.js    | 20 LTS+         | Next.js frontend runtime       |
| Nginx      | 1.24+           | Reverse proxy, SSL termination |
| Composer   | 2.x             | PHP dependency management      |
| Supervisor | 4.x             | Process management for workers |
| Certbot    | latest           | SSL certificate management     |
| PM2        | latest           | Node.js process management     |

---

## Server Architecture

```
                    ┌─────────────────────────────────────────┐
                    │              Internet                     │
                    └───────────────┬─────────────────────────┘
                                    │
                                    ▼
                    ┌─────────────────────────────────────────┐
                    │         Nginx (port 80/443)              │
                    │   - SSL termination                      │
                    │   - Wildcard server block                │
                    │   - Routes by request path               │
                    └──────┬────────────────────┬─────────────┘
                           │                    │
                    /api/* │                    │ Everything else
                           ▼                    ▼
              ┌─────────────────────┐  ┌─────────────────────┐
              │  Laravel (PHP-FPM)  │  │  Next.js (PM2)      │
              │  127.0.0.1:9000     │  │  127.0.0.1:3000     │
              │  - REST API         │  │  - SSR/ISR pages    │
              │  - Admin panel API  │  │  - Public frontend  │
              └──────┬──────────────┘  └─────────────────────┘
                     │
          ┌──────────┼──────────┐
          ▼          ▼          ▼
    ┌──────────┐ ┌────────┐ ┌────────────────────────┐
    │  MySQL   │ │ Redis  │ │  Supervisor             │
    │  - cms_  │ │ Cache  │ │  - queue:work           │
    │    main  │ │ Queue  │ │  - tenant DB creation   │
    │  - ten-  │ │ Session│ └────────────────────────┘
    │    ant_* │ └────────┘
    └──────────┘
```

The system uses a **single wildcard Nginx server block** that catches all incoming domains. Based on the request path:

- Requests to `/api/*` are forwarded to the Laravel backend (PHP-FPM on port 9000).
- All other requests are forwarded to the Next.js frontend (PM2 on port 3000).

The Laravel backend resolves the requesting domain against the `sites` table in the landlord database (`cms_main`) and dynamically connects to the corresponding tenant database (`tenant_{site_id}`).

---

## 1. Server Setup

### Install system dependencies

```bash
# Update system packages
sudo apt update && sudo apt upgrade -y

# Install essential tools
sudo apt install -y curl wget git unzip software-properties-common

# Install PHP 8.2 and required extensions
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2-fpm php8.2-cli php8.2-mysql php8.2-redis \
    php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-bcmath \
    php8.2-gd php8.2-intl php8.2-readline

# Install MySQL 8.0
sudo apt install -y mysql-server

# Install Redis
sudo apt install -y redis-server

# Install Node.js 20 LTS
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Install Nginx
sudo apt install -y nginx

# Install Supervisor
sudo apt install -y supervisor

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install PM2 globally
sudo npm install -g pm2

# Install Certbot for SSL
sudo apt install -y certbot python3-certbot-nginx
```

### Create system user

```bash
# Create a dedicated application user
sudo useradd -m -s /bin/bash cmsapp
sudo usermod -aG www-data cmsapp

# Create application directory
sudo mkdir -p /var/www/multi-tenant-cms
sudo chown cmsapp:www-data /var/www/multi-tenant-cms
```

### Deploy application code

```bash
# As the cmsapp user
sudo su - cmsapp

# Clone or copy the application
# Option 1: Git clone
git clone <your-repo-url> /var/www/multi-tenant-cms

# Option 2: rsync from local machine (run from local)
# rsync -avz --exclude=node_modules --exclude=vendor ./multi-tenant-cms/ cmsapp@server:/var/www/multi-tenant-cms/
```

---

## 2. MySQL Setup

### Secure MySQL installation

```bash
sudo mysql_secure_installation
```

### Create landlord database and application user

```sql
-- Connect as root
sudo mysql -u root -p

-- Create the landlord database
CREATE DATABASE IF NOT EXISTS `cms_main`
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

-- Create application user with CREATE DATABASE privilege
-- (required for automatic tenant database creation)
CREATE USER 'cms_user'@'localhost' IDENTIFIED BY 'YOUR_STRONG_PASSWORD_HERE';

-- Grant privileges on the landlord database
GRANT ALL PRIVILEGES ON `cms_main`.* TO 'cms_user'@'localhost';

-- Grant CREATE privilege globally (needed for creating tenant databases)
GRANT CREATE ON *.* TO 'cms_user'@'localhost';

-- Grant full access to any tenant database (tenant_*)
GRANT ALL PRIVILEGES ON `tenant\_%`.* TO 'cms_user'@'localhost';

FLUSH PRIVILEGES;
```

### Import schema (Option A: raw SQL)

```bash
# Import only the landlord schema
mysql -u cms_user -p cms_main < /var/www/multi-tenant-cms/docs/schema.sql
```

Note: The tenant database tables are created automatically by the `tenant:create` command or when adding a site through the admin panel.

### Run migrations (Option B: Laravel migrations -- recommended)

```bash
cd /var/www/multi-tenant-cms/backend

# Run landlord migrations
php artisan migrate --path=database/migrations/landlord --database=landlord

# Run base Laravel migrations (users, cache, sessions, etc.)
php artisan migrate --database=landlord
```

### MySQL production tuning

Add to `/etc/mysql/mysql.conf.d/mysqld.cnf`:

```ini
[mysqld]
# Connection limits (increase for many tenant databases)
max_connections = 500
wait_timeout = 28800
interactive_timeout = 28800

# InnoDB tuning
innodb_buffer_pool_size = 1G          # Set to ~70% of available RAM
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2    # Slight durability trade-off for performance
innodb_flush_method = O_DIRECT

# Query cache (MySQL 8.0 removed query cache, use Redis instead)
# Character set
character-set-server = utf8mb4
collation-server = utf8mb4_unicode_ci

# Logging
slow_query_log = 1
slow_query_log_file = /var/log/mysql/slow.log
long_query_time = 2
```

Restart MySQL after changes:

```bash
sudo systemctl restart mysql
```

---

## 3. Laravel Backend Deployment

### Install dependencies

```bash
cd /var/www/multi-tenant-cms/backend

# Install PHP dependencies (production mode)
composer install --no-dev --optimize-autoloader
```

### Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with production values:

```dotenv
# ================================================
# Application
# ================================================
APP_NAME="Multi-Tenant CMS"
APP_ENV=production
APP_KEY=base64:GENERATED_KEY_HERE
APP_DEBUG=false
APP_URL=https://admin.yourdomain.com

# ================================================
# Database (Landlord)
# ================================================
DB_CONNECTION=landlord
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cms_main
DB_USERNAME=cms_user
DB_PASSWORD=YOUR_STRONG_PASSWORD_HERE

# ================================================
# Redis
# ================================================
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=YOUR_REDIS_PASSWORD
REDIS_PORT=6379

# ================================================
# Cache & Session
# ================================================
CACHE_STORE=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

# ================================================
# Queue
# ================================================
QUEUE_CONNECTION=redis

# ================================================
# Sanctum
# ================================================
SANCTUM_STATEFUL_DOMAINS=admin.yourdomain.com
SESSION_DOMAIN=.yourdomain.com

# ================================================
# Logging
# ================================================
LOG_CHANNEL=daily
LOG_LEVEL=warning

# ================================================
# Filesystem
# ================================================
FILESYSTEM_DISK=public
```

### Run migrations

```bash
cd /var/www/multi-tenant-cms/backend

# Run base Laravel migrations (users, cache, sessions, personal_access_tokens)
php artisan migrate --database=landlord

# Run landlord-specific migrations (sites, global_top_offers)
php artisan migrate --path=database/migrations/landlord --database=landlord
```

### Create first admin user

```bash
php artisan tinker
```

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Admin',
    'email' => 'admin@yourdomain.com',
    'password' => Hash::make('your-secure-password'),
    'email_verified_at' => now(),
]);
```

### Set up storage and permissions

```bash
cd /var/www/multi-tenant-cms/backend

# Create storage symlink
php artisan storage:link

# Set proper permissions
sudo chown -R cmsapp:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Configure PHP-FPM

Edit `/etc/php/8.2/fpm/pool.d/cms.conf`:

```ini
[cms]
user = cmsapp
group = www-data
listen = /run/php/php8.2-fpm-cms.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660

pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 1000

; Environment variables
env[DB_HOST] = 127.0.0.1
env[DB_PORT] = 3306

; PHP settings
php_admin_value[error_log] = /var/log/php/cms-error.log
php_admin_value[memory_limit] = 256M
php_admin_value[max_execution_time] = 60
php_admin_value[upload_max_filesize] = 20M
php_admin_value[post_max_size] = 25M
```

```bash
sudo mkdir -p /var/log/php
sudo systemctl restart php8.2-fpm
```

---

## 4. Next.js Frontend Deployment

### Install dependencies and build

```bash
cd /var/www/multi-tenant-cms/frontend

# Install production dependencies
npm ci --production

# Configure environment
cat > .env.local << 'EOF'
NEXT_PUBLIC_API_URL=https://yourdomain.com/api
API_URL=http://127.0.0.1:9000/api
EOF

# Build the application
npm run build
```

### Start with PM2

```bash
# Start the Next.js application
cd /var/www/multi-tenant-cms/frontend
pm2 start npm --name "cms-frontend" -- start

# Configure PM2 to start on boot
pm2 save
pm2 startup
# Follow the instructions printed by the startup command
```

### PM2 ecosystem file (alternative)

Create `/var/www/multi-tenant-cms/frontend/ecosystem.config.js`:

```javascript
module.exports = {
  apps: [
    {
      name: 'cms-frontend',
      cwd: '/var/www/multi-tenant-cms/frontend',
      script: 'npm',
      args: 'start',
      instances: 'max',        // Use all available CPU cores
      exec_mode: 'cluster',    // Cluster mode for load balancing
      max_memory_restart: '512M',
      env: {
        NODE_ENV: 'production',
        PORT: 3000,
      },
    },
  ],
};
```

```bash
pm2 start ecosystem.config.js
pm2 save
```

---

## 5. Nginx Configuration

### Main Nginx configuration

Create `/etc/nginx/sites-available/multi-tenant-cms`:

```nginx
# =============================================
# Multi-Tenant CMS - Nginx Configuration
# =============================================
# This is a wildcard server block that catches ALL domains
# pointing to this server. No per-site configuration needed.
# =============================================

# Upstream for Next.js
upstream nextjs_upstream {
    server 127.0.0.1:3000;
    keepalive 64;
}

# Redirect HTTP to HTTPS
server {
    listen 80;
    listen [::]:80;
    server_name _;

    # Allow Let's Encrypt ACME challenge
    location /.well-known/acme-challenge/ {
        root /var/www/certbot;
        allow all;
    }

    location / {
        return 301 https://$host$request_uri;
    }
}

# Main HTTPS server block (wildcard - catches all domains)
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name _;

    # -------------------------------------------
    # SSL Configuration
    # -------------------------------------------
    # Default certificate (replace with your wildcard or primary cert)
    # Individual per-domain certs are managed by Certbot and loaded
    # automatically via ssl_certificate_by_lua or SNI.
    ssl_certificate /etc/letsencrypt/live/default/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/default/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 1d;
    ssl_session_tickets off;

    # HSTS (optional, enable after confirming SSL works)
    # add_header Strict-Transport-Security "max-age=63072000" always;

    # -------------------------------------------
    # General Settings
    # -------------------------------------------
    root /var/www/multi-tenant-cms/backend/public;
    index index.php;
    charset utf-8;

    client_max_body_size 20M;

    # Gzip compression
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript image/svg+xml;
    gzip_vary on;
    gzip_min_length 1024;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    # -------------------------------------------
    # API Routes -> Laravel (PHP-FPM)
    # -------------------------------------------
    location /api {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Sanctum CSRF cookie route
    location /sanctum {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM handler
    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.2-fpm-cms.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;

        fastcgi_buffer_size 16k;
        fastcgi_buffers 16 16k;
        fastcgi_read_timeout 60s;

        # Pass the original host header to Laravel for domain resolution
        fastcgi_param HTTP_HOST $host;
        fastcgi_param SERVER_NAME $host;
    }

    # Laravel storage files
    location /storage {
        alias /var/www/multi-tenant-cms/backend/storage/app/public;
        expires 30d;
        add_header Cache-Control "public, immutable";
    }

    # -------------------------------------------
    # Static assets from Next.js
    # -------------------------------------------
    location /_next/static {
        proxy_pass http://nextjs_upstream;
        proxy_http_version 1.1;
        proxy_set_header Connection "";
        expires 365d;
        add_header Cache-Control "public, immutable";
    }

    # -------------------------------------------
    # Everything else -> Next.js
    # -------------------------------------------
    location / {
        proxy_pass http://nextjs_upstream;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_set_header X-Forwarded-Host $host;
        proxy_cache_bypass $http_upgrade;

        proxy_buffer_size 16k;
        proxy_buffers 16 16k;
        proxy_read_timeout 60s;
    }

    # -------------------------------------------
    # Deny access to hidden files
    # -------------------------------------------
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Enable the site

```bash
sudo ln -s /etc/nginx/sites-available/multi-tenant-cms /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default

# Test configuration
sudo nginx -t

# Reload Nginx
sudo systemctl reload nginx
```

### SSL with Let's Encrypt

For individual domains (run for each site domain):

```bash
# Create webroot for ACME challenges
sudo mkdir -p /var/www/certbot

# Issue certificate for a specific domain
sudo certbot certonly --webroot -w /var/www/certbot -d example.com

# For the first domain, create the default symlink
sudo ln -s /etc/letsencrypt/live/example.com /etc/letsencrypt/live/default
```

For wildcard certificates (if all sites share a parent domain):

```bash
# Wildcard cert requires DNS validation
sudo certbot certonly --manual --preferred-challenges=dns \
    -d "*.yourdomain.com" -d "yourdomain.com"
```

Auto-renewal is configured automatically by Certbot. Verify with:

```bash
sudo certbot renew --dry-run
```

### Nginx with multiple SSL certificates (SNI)

If sites use different domains, use Certbot's Nginx plugin to manage per-domain certificates automatically:

```bash
# This modifies the Nginx config to add per-domain SSL blocks
sudo certbot --nginx -d example1.com -d example2.com
```

Alternatively, for many domains, consider using a certificate management tool or a reverse proxy like Caddy that handles SSL automatically.

---

## 6. Redis Configuration

### Secure Redis for production

Edit `/etc/redis/redis.conf`:

```ini
# Bind to localhost only
bind 127.0.0.1 ::1

# Require authentication
requirepass YOUR_REDIS_PASSWORD

# Memory management
maxmemory 512mb
maxmemory-policy allkeys-lru

# Disable dangerous commands
rename-command FLUSHALL ""
rename-command FLUSHDB ""
rename-command CONFIG ""
rename-command DEBUG ""

# Persistence (AOF for durability)
appendonly yes
appendfsync everysec

# Connection limits
maxclients 10000
timeout 300

# Logging
loglevel warning
logfile /var/log/redis/redis-server.log
```

```bash
sudo systemctl restart redis-server
```

### Redis usage in the CMS

| Redis Database | Purpose                              |
|----------------|--------------------------------------|
| DB 0           | Default (queue, general)             |
| DB 1           | Cache (site lookups, page cache)     |
| DB 2           | Sessions (optional)                  |

Key patterns used:

- `site:domain:{domain}` -- Cached site lookups (TTL: 10 minutes)
- `laravel_cache:*` -- General Laravel cache entries
- `laravel_database_queues:*` -- Queue jobs

---

## 7. Queue Worker (Supervisor)

### Supervisor configuration

Create `/etc/supervisor/conf.d/cms-worker.conf`:

```ini
[program:cms-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/multi-tenant-cms/backend/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600 --max-jobs=1000
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=cmsapp
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/supervisor/cms-worker.log
stdout_logfile_maxbytes=10MB
stdout_logfile_backups=5
stopwaitsecs=3600
```

### Start the workers

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start cms-worker:*

# Check worker status
sudo supervisorctl status
```

### Restart workers after deployments

```bash
# Gracefully restart workers (finishes current jobs first)
php /var/www/multi-tenant-cms/backend/artisan queue:restart
```

---

## 8. Performance Optimization

### Laravel optimizations

Run these after every deployment:

```bash
cd /var/www/multi-tenant-cms/backend

# Cache configuration files
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Run all optimizations at once
php artisan optimize

# Clear and rebuild if needed
php artisan optimize:clear
php artisan optimize
```

### OPcache configuration

Edit `/etc/php/8.2/fpm/conf.d/10-opcache.ini`:

```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0          ; Disable in production (clear manually on deploy)
opcache.save_comments=1
opcache.enable_file_override=1
```

### Next.js optimizations

The Next.js frontend uses ISR (Incremental Static Regeneration) for optimal performance:

- Static pages are generated at build time and revalidated on request.
- The `revalidate` setting in page components controls the cache TTL.
- Image optimization is handled by the Next.js Image component.

Recommended ISR revalidation times:

| Content Type  | Revalidate (seconds) | Reasoning                      |
|---------------|----------------------|--------------------------------|
| Homepage      | 300 (5 min)          | Moderate update frequency      |
| Blog posts    | 3600 (1 hour)        | Infrequent changes             |
| Static pages  | 600 (10 min)         | Occasional updates             |
| Top offers    | 300 (5 min)          | May change frequently          |

### Redis cache TTLs

| Cache Key Pattern       | TTL          | Purpose                    |
|-------------------------|--------------|----------------------------|
| `site:domain:*`         | 10 minutes   | Site/domain resolution     |
| Page content            | 30 minutes   | Full page data             |
| Post listings           | 15 minutes   | Blog post lists            |
| Global offers           | 10 minutes   | Global top offers          |

### MySQL query optimization

- All frequently queried columns have indexes (see schema.sql).
- The `slug` columns are indexed for fast lookups.
- The `is_active` and `is_published` columns are indexed for filtered queries.
- Use `EXPLAIN` to analyze slow queries logged in `/var/log/mysql/slow.log`.

### CDN recommendations

For static assets and media files, consider placing a CDN in front of the server:

- **Cloudflare** (free tier available) -- DNS proxy with caching and DDoS protection.
- **AWS CloudFront** -- If hosting on AWS.
- **BunnyCDN** -- Cost-effective option for media-heavy sites.

Configure CDN to cache:
- `/_next/static/*` -- Immutable Next.js build assets (cache forever).
- `/storage/*` -- Uploaded media files (cache 30 days).
- HTML pages -- Cache with short TTL (5 min) or use stale-while-revalidate.

---

## 9. Scaling Considerations

### For up to 50 sites (single server)

A single server with the following specs handles 50 sites comfortably:

| Resource | Recommended       |
|----------|-------------------|
| CPU      | 4 vCPUs           |
| RAM      | 8 GB              |
| Storage  | 100 GB SSD        |
| Network  | 1 Gbps            |

This configuration supports approximately:
- 50 tenant databases
- ~500 concurrent users across all sites
- ~10,000 daily page views per site

### For 100-500 sites (vertical scaling + separation)

| Component     | Recommendation                                     |
|---------------|---------------------------------------------------|
| MySQL         | Dedicated database server (16 GB RAM, SSD)        |
| Redis         | Dedicated Redis instance (4 GB RAM)               |
| Application   | 2-3 application servers behind a load balancer    |
| Next.js       | Scale PM2 cluster to use all CPU cores            |

### For 500+ sites (horizontal scaling)

```
                    ┌──────────────┐
                    │  Load        │
                    │  Balancer    │
                    └──┬───────┬──┘
                       │       │
              ┌────────▼──┐ ┌─▼────────┐
              │  App       │ │  App      │
              │  Server 1  │ │  Server 2 │
              │  (Laravel  │ │  (Laravel │
              │  + Next.js)│ │  + Next.js│
              └──────┬─────┘ └────┬─────┘
                     │            │
              ┌──────▼────────────▼──────┐
              │     MySQL Primary         │
              │     (Write)               │
              └──────┬────────────┬──────┘
                     │            │
              ┌──────▼──┐  ┌─────▼──────┐
              │  MySQL   │  │  MySQL     │
              │  Replica │  │  Replica   │
              │  (Read)  │  │  (Read)    │
              └─────────┘  └────────────┘
```

Key considerations:

- **Database connection limits**: Each tenant requires its own database connection. With 500 tenants and connection pooling, plan for `max_connections = 2000+` on MySQL.
- **Redis memory**: Each cached site entry is approximately 2 KB. For 500 sites: ~1 MB for site cache alone. Plan 1-2 GB for Redis total.
- **Session storage**: With Redis sessions, ensure Redis has enough memory. Consider using a dedicated Redis instance for sessions.
- **File storage**: Move uploaded files to S3 or equivalent object storage. Update Laravel filesystem config to use the `s3` driver.
- **Database backups**: With 500+ tenant databases, backups become time-consuming. Use MySQL replication and back up from the replica.

### Connection pooling

For high-traffic deployments, consider using a connection pooler like ProxySQL:

```bash
# ProxySQL sits between Laravel and MySQL
# Pools and reuses database connections
# Reduces the connection overhead for many tenant databases
```

---

## 10. Monitoring and Maintenance

### Log rotation

Laravel logs are rotated automatically when using the `daily` log channel. For other logs, configure logrotate:

Create `/etc/logrotate.d/multi-tenant-cms`:

```
/var/log/supervisor/cms-worker.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
}

/var/log/php/cms-error.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    postrotate
        systemctl reload php8.2-fpm
    endscript
}
```

### Database backups

Create `/usr/local/bin/backup-cms-databases.sh`:

```bash
#!/bin/bash
# Backup all CMS databases (landlord + all tenants)

BACKUP_DIR="/var/backups/cms"
DATE=$(date +%Y-%m-%d_%H%M)
MYSQL_USER="cms_user"
MYSQL_PASS="YOUR_STRONG_PASSWORD_HERE"
RETENTION_DAYS=30

mkdir -p "$BACKUP_DIR"

# Backup landlord database
mysqldump -u "$MYSQL_USER" -p"$MYSQL_PASS" cms_main | gzip > "$BACKUP_DIR/cms_main_$DATE.sql.gz"

# Backup all tenant databases
mysql -u "$MYSQL_USER" -p"$MYSQL_PASS" -N -e "SELECT db_name FROM cms_main.sites WHERE is_active = 1" | while read DB_NAME; do
    mysqldump -u "$MYSQL_USER" -p"$MYSQL_PASS" "$DB_NAME" | gzip > "$BACKUP_DIR/${DB_NAME}_$DATE.sql.gz"
done

# Remove backups older than retention period
find "$BACKUP_DIR" -name "*.sql.gz" -mtime +$RETENTION_DAYS -delete

echo "[$(date)] Backup completed. Files in $BACKUP_DIR"
```

```bash
chmod +x /usr/local/bin/backup-cms-databases.sh

# Add to crontab (daily at 2 AM)
echo "0 2 * * * /usr/local/bin/backup-cms-databases.sh >> /var/log/cms-backup.log 2>&1" | sudo crontab -
```

### Health check endpoints

The Laravel backend should expose a health check endpoint. Add to your routes:

```php
// routes/api.php
Route::get('/health', function () {
    try {
        DB::connection('landlord')->getPdo();
        Cache::store('redis')->get('health-check');
        return response()->json(['status' => 'ok', 'timestamp' => now()]);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
});
```

Monitor with a cron job or external service:

```bash
# Simple health check cron (every 5 minutes)
*/5 * * * * curl -sf https://yourdomain.com/api/health > /dev/null || echo "CMS health check failed" | mail -s "CMS Alert" admin@yourdomain.com
```

### Cache clearing commands

```bash
cd /var/www/multi-tenant-cms/backend

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Clear specific site cache
php artisan tinker --execute="Cache::forget('site:domain:example.com')"

# Rebuild all caches
php artisan optimize
```

---

## 11. Adding a New Site

Adding a new site to the CMS requires four steps:

### Step 1: Create the site via admin panel or CLI

**Option A: Admin Panel**

Use the admin panel to create a new site. This automatically creates the site record in the landlord database and provisions the tenant database.

**Option B: Artisan Command**

```bash
cd /var/www/multi-tenant-cms/backend
php artisan tenant:create example.com "Example Site"
```

This command:
1. Creates a record in the `sites` table of `cms_main`.
2. Auto-generates the `db_name` as `tenant_{site_id}`.
3. Creates the tenant database.
4. Runs all tenant migrations on the new database.

### Step 2: Configure DNS

Point the new domain's A record to the server IP:

```
example.com.    IN    A    YOUR_SERVER_IP
www.example.com. IN  CNAME example.com.
```

### Step 3: Issue SSL certificate

```bash
sudo certbot certonly --webroot -w /var/www/certbot -d example.com -d www.example.com

# Reload Nginx to pick up the new certificate
sudo systemctl reload nginx
```

### Step 4: Verify

No Nginx configuration change is needed because the wildcard server block catches all domains automatically.

```bash
# Test the new site
curl -I https://example.com
curl https://example.com/api/health
```

---

## 12. Artisan Commands Reference

### tenant:create

Creates a new tenant with its own database.

```bash
php artisan tenant:create {domain} {name}

# Example
php artisan tenant:create example.com "Example Site"
```

### tenant:migrate-all

Runs tenant migrations on all active tenant databases.

```bash
php artisan tenant:migrate-all

# Useful after adding new tenant migrations
```

### tenant:seed

Seeds sample data into a specific tenant database.

```bash
php artisan tenant:seed {domain}

# Example
php artisan tenant:seed example.com
```

### Other useful commands

```bash
# List all sites
php artisan tinker --execute="App\Models\Site::all(['id','domain','name','db_name','is_active'])->toArray()"

# Deactivate a site
php artisan tinker --execute="App\Models\Site::where('domain','example.com')->update(['is_active'=>false])"

# Clear tenant cache for a domain
php artisan tinker --execute="Cache::forget('site:domain:example.com')"
```

---

## Troubleshooting

### Common issues

**502 Bad Gateway**
- Check if PHP-FPM is running: `sudo systemctl status php8.2-fpm`
- Check if Next.js is running: `pm2 status`
- Check Nginx error log: `sudo tail -f /var/log/nginx/error.log`

**Database connection refused**
- Verify MySQL is running: `sudo systemctl status mysql`
- Check credentials in `.env`
- Verify the MySQL user has proper grants: `SHOW GRANTS FOR 'cms_user'@'localhost';`

**Tenant database not found**
- Check the `sites` table: `SELECT domain, db_name FROM cms_main.sites;`
- Verify the tenant database exists: `SHOW DATABASES LIKE 'tenant_%';`
- Re-run tenant creation: `php artisan tenant:create domain.com "Site Name"`

**Cache issues / stale data**
- Clear all caches: `php artisan optimize:clear`
- Clear Redis: `redis-cli -a YOUR_REDIS_PASSWORD FLUSHDB`
- Restart workers: `php artisan queue:restart`

**Permission denied errors**
- Fix storage permissions: `sudo chown -R cmsapp:www-data storage bootstrap/cache && sudo chmod -R 775 storage bootstrap/cache`

### Deployment checklist

Use this checklist for each deployment:

```
[ ] Pull latest code
[ ] composer install --no-dev --optimize-autoloader
[ ] npm ci --production && npm run build (frontend)
[ ] php artisan migrate --database=landlord
[ ] php artisan migrate --path=database/migrations/landlord --database=landlord
[ ] php artisan tenant:migrate-all
[ ] php artisan optimize
[ ] pm2 restart cms-frontend
[ ] php artisan queue:restart
[ ] sudo systemctl reload php8.2-fpm
[ ] sudo systemctl reload nginx
[ ] Verify health check: curl https://yourdomain.com/api/health
```
