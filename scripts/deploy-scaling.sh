#!/bin/bash
# Multi-Tenant CMS — Scaling Deployment Script
# Run on server: bash /var/www/multi-tenant-cms/scripts/deploy-scaling.sh
# Each section can be run independently by passing the section name as argument.

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
PROJECT_DIR="/var/www/multi-tenant-cms"
BACKEND_DIR="${PROJECT_DIR}/backend"
FRONTEND_DIR="${PROJECT_DIR}/frontend"

log() { echo -e "\n\033[1;32m[$(date '+%H:%M:%S')] $1\033[0m"; }
warn() { echo -e "\033[1;33m[WARNING] $1\033[0m"; }
err() { echo -e "\033[1;31m[ERROR] $1\033[0m"; }

# ============================================================
# SECTION 1: Fix ilkbahisonline.com nginx (if per-site configs still exist)
# ============================================================
fix_ilkbahisonline() {
    log "Checking ilkbahisonline.com nginx config..."

    # Check for proxy_pass 8000 in any config
    BAD_CONFIGS=$(grep -rl "proxy_pass.*8000" /etc/nginx/sites-enabled/ 2>/dev/null || true)
    if [ -n "$BAD_CONFIGS" ]; then
        warn "Found configs with proxy_pass 8000:"
        echo "$BAD_CONFIGS"
        warn "These will be replaced by the wildcard config. Run 'setup_wildcard_nginx' to fix."
    else
        log "No proxy_pass 8000 issues found."
    fi
}

# ============================================================
# SECTION 2: PM2 cache cleanup + ecosystem config
# ============================================================
setup_pm2() {
    log "Setting up PM2 with ecosystem config..."

    pm2 stop cms-frontend 2>/dev/null || true
    pm2 delete cms-frontend 2>/dev/null || true

    log "Clearing old build cache..."
    rm -rf "${FRONTEND_DIR}/.next"

    log "Building frontend..."
    cd "${FRONTEND_DIR}"
    npm run build

    log "Starting PM2 with ecosystem config..."
    pm2 start "${FRONTEND_DIR}/ecosystem.config.js"
    pm2 save

    log "PM2 status:"
    pm2 status
}

# ============================================================
# SECTION 3: Supervisor queue worker
# ============================================================
setup_supervisor() {
    log "Setting up Supervisor queue worker..."

    # Install supervisor if not present
    if ! command -v supervisorctl &>/dev/null; then
        apt-get install -y supervisor
    fi

    cp "${SCRIPT_DIR}/supervisor-worker.conf" /etc/supervisor/conf.d/cms-worker.conf

    supervisorctl reread
    supervisorctl update
    supervisorctl start cms-worker:* 2>/dev/null || true

    log "Supervisor status:"
    supervisorctl status cms-worker:*
}

# ============================================================
# SECTION 4: Run tenant migrations
# ============================================================
run_migrations() {
    log "Running tenant migrations on all sites..."
    cd "${BACKEND_DIR}"
    php artisan tenant:migrate-all --force
}

# ============================================================
# SECTION 5: Wildcard Nginx setup
# ============================================================
setup_wildcard_nginx() {
    log "Setting up wildcard Nginx config..."

    # Create SSL directory
    mkdir -p /etc/nginx/ssl

    # Check for Cloudflare origin certificate
    if [ ! -f /etc/nginx/ssl/cloudflare-origin.pem ]; then
        err "Cloudflare Origin Certificate not found at /etc/nginx/ssl/cloudflare-origin.pem"
        echo "Steps to create:"
        echo "  1. Go to Cloudflare Dashboard > SSL/TLS > Origin Server"
        echo "  2. Create Certificate (15 year, wildcard *)"
        echo "  3. Save certificate to /etc/nginx/ssl/cloudflare-origin.pem"
        echo "  4. Save private key to /etc/nginx/ssl/cloudflare-origin-key.pem"
        echo "  5. Run this script again."
        return 1
    fi

    # Install wildcard config
    cp "${SCRIPT_DIR}/nginx-wildcard.conf" /etc/nginx/sites-available/cms-wildcard

    # Enable it
    ln -sf /etc/nginx/sites-available/cms-wildcard /etc/nginx/sites-enabled/cms-wildcard

    # Test config
    log "Testing nginx config..."
    nginx -t

    # Remove old per-site configs (backup first)
    log "Backing up old per-site configs..."
    mkdir -p /etc/nginx/sites-backup
    for conf in /etc/nginx/sites-enabled/*; do
        name=$(basename "$conf")
        if [ "$name" != "cms-wildcard" ] && [ "$name" != "default" ]; then
            mv "/etc/nginx/sites-available/$name" /etc/nginx/sites-backup/ 2>/dev/null || true
            rm -f "$conf"
        fi
    done

    # Remove default if it exists
    rm -f /etc/nginx/sites-enabled/default

    # Test and reload
    nginx -t && systemctl reload nginx
    log "Wildcard Nginx config active. All per-site configs backed up to /etc/nginx/sites-backup/"
}

# ============================================================
# SECTION 6: Redis cache switch
# ============================================================
setup_redis() {
    log "Switching to Redis cache..."

    # Check if Redis is running
    if ! systemctl is-active --quiet redis-server 2>/dev/null && ! systemctl is-active --quiet redis 2>/dev/null; then
        log "Installing Redis..."
        apt-get install -y redis-server
        systemctl enable redis-server
        systemctl start redis-server
    fi

    cd "${BACKEND_DIR}"

    # Update .env
    sed -i 's/^CACHE_STORE=.*/CACHE_STORE=redis/' .env 2>/dev/null || echo "CACHE_STORE=redis" >> .env
    sed -i 's/^SESSION_DRIVER=.*/SESSION_DRIVER=redis/' .env 2>/dev/null || echo "SESSION_DRIVER=redis" >> .env
    sed -i 's/^QUEUE_CONNECTION=.*/QUEUE_CONNECTION=redis/' .env 2>/dev/null || echo "QUEUE_CONNECTION=redis" >> .env

    # Clear old cache
    php artisan cache:clear
    php artisan config:clear

    log "Redis cache configured."
}

# ============================================================
# SECTION 7: MySQL tuning
# ============================================================
tune_mysql() {
    log "Tuning MySQL for 2000 sites..."

    MYSQL_CONF="/etc/mysql/mysql.conf.d/mysqld.cnf"
    if [ ! -f "$MYSQL_CONF" ]; then
        MYSQL_CONF="/etc/mysql/my.cnf"
    fi

    # Backup
    cp "$MYSQL_CONF" "${MYSQL_CONF}.bak"

    # Add/update settings
    if grep -q "max_connections" "$MYSQL_CONF"; then
        sed -i 's/^max_connections.*/max_connections = 500/' "$MYSQL_CONF"
    else
        echo "max_connections = 500" >> "$MYSQL_CONF"
    fi

    if grep -q "wait_timeout" "$MYSQL_CONF"; then
        sed -i 's/^wait_timeout.*/wait_timeout = 60/' "$MYSQL_CONF"
    else
        echo "wait_timeout = 60" >> "$MYSQL_CONF"
    fi

    if grep -q "interactive_timeout" "$MYSQL_CONF"; then
        sed -i 's/^interactive_timeout.*/interactive_timeout = 60/' "$MYSQL_CONF"
    else
        echo "interactive_timeout = 60" >> "$MYSQL_CONF"
    fi

    if grep -q "thread_cache_size" "$MYSQL_CONF"; then
        sed -i 's/^thread_cache_size.*/thread_cache_size = 128/' "$MYSQL_CONF"
    else
        echo "thread_cache_size = 128" >> "$MYSQL_CONF"
    fi

    log "MySQL config updated. Restart MySQL to apply:"
    echo "  systemctl restart mysql"
}

# ============================================================
# SECTION 8: PM2 Cluster Mode (Aşama 2.5)
# ============================================================
setup_pm2_cluster() {
    log "Upgrading PM2 to cluster mode..."

    pm2 stop cms-frontend 2>/dev/null || true
    pm2 delete cms-frontend 2>/dev/null || true

    # Update ecosystem config for cluster mode
    cat > "${FRONTEND_DIR}/ecosystem.config.js" << 'ECEOF'
module.exports = {
  apps: [{
    name: 'cms-frontend',
    script: 'npm',
    args: 'start',
    cwd: '/var/www/multi-tenant-cms/frontend',
    instances: 4,
    exec_mode: 'cluster',
    max_memory_restart: '2G',
    max_restarts: 10,
    min_uptime: '10s',
    restart_delay: 5000,
    env: {
      NODE_ENV: 'production',
      PORT: 3000,
    },
  }],
};
ECEOF

    pm2 start "${FRONTEND_DIR}/ecosystem.config.js"
    pm2 save

    log "PM2 cluster mode active (4 instances)."
    pm2 status
}

# ============================================================
# SECTION 9: System updates
# ============================================================
system_update() {
    log "Running system updates..."
    apt update && apt upgrade -y
    log "System updated."
}

# ============================================================
# Main: run specific section or all
# ============================================================
if [ $# -gt 0 ]; then
    case "$1" in
        fix_ilkbahisonline) fix_ilkbahisonline ;;
        setup_pm2) setup_pm2 ;;
        setup_supervisor) setup_supervisor ;;
        run_migrations) run_migrations ;;
        setup_wildcard_nginx) setup_wildcard_nginx ;;
        setup_redis) setup_redis ;;
        tune_mysql) tune_mysql ;;
        setup_pm2_cluster) setup_pm2_cluster ;;
        system_update) system_update ;;
        *)
            echo "Usage: $0 [section]"
            echo "Sections: fix_ilkbahisonline setup_pm2 setup_supervisor run_migrations"
            echo "          setup_wildcard_nginx setup_redis tune_mysql setup_pm2_cluster system_update"
            echo "Run without arguments to execute all Aşama 1 tasks."
            exit 1
            ;;
    esac
else
    log "=== AŞAMA 1: Acil Düzeltmeler ==="
    fix_ilkbahisonline
    setup_pm2
    setup_supervisor
    run_migrations

    log "=== Aşama 1 tamamlandı! ==="
    echo ""
    echo "Sonraki adımlar (Aşama 2):"
    echo "  1. Cloudflare Origin Certificate oluştur ve /etc/nginx/ssl/ altına kaydet"
    echo "  2. bash $0 setup_wildcard_nginx"
    echo "  3. bash $0 setup_redis"
    echo "  4. bash $0 tune_mysql && systemctl restart mysql"
    echo "  5. bash $0 setup_pm2_cluster"
fi
