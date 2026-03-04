module.exports = {
  apps: [{
    name: 'cms-frontend',
    script: 'npm',
    args: 'start',
    cwd: '/var/www/multi-tenant-cms/frontend',
    instances: 1,
    exec_mode: 'fork',
    max_memory_restart: '1G',
    max_restarts: 10,
    min_uptime: '10s',
    restart_delay: 5000,
    env: {
      NODE_ENV: 'production',
      PORT: 3000,
    },
  }],
};
