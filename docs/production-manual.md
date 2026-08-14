# Navkwa Production Manual

This manual defines the production deployment standard for the Navkwa website and the future Navkwa Build ERP/API/cloud console.

The target architecture is:

```text
Internet
  -> Cloudflare
  -> Ubuntu 24.04 LTS
  -> UFW Firewall
  -> Fail2Ban
  -> Nginx
  -> PHP-FPM
  -> Navkwa Website and Navkwa Build
  -> PostgreSQL
  -> Redis
  -> Queue Workers
  -> Laravel Scheduler
```

The goal is not to memorize commands. For every component, learn:

- What are we installing?
- Why do we need it?
- What problem does it solve?
- How do we verify it is working?
- How do we troubleshoot it?

## Phase 1: Infrastructure

Status: completed.

Includes:

- Domain
- DNS
- Cloudflare
- VPS
- Ubuntu
- SSH
- Administrator account
- Git

Production server:

| Field | Value |
| --- | --- |
| Provider | Hetzner Cloud |
| Project | Navkwa Production |
| Server name | navkwa-prod-01 |
| Public IPv4 | 49.12.103.75 |
| Location | Falkenstein, Germany |
| Region | eu-central |
| Plan | CPX22, x86, 80 GB |
| OS target | Ubuntu 24.04 LTS |

Production rule: keep DNS, server access, GitHub access, and server credentials under clear ownership. Do not put private keys, API tokens, production passwords, or payment credentials in Git.

The source-of-truth server inventory is `deploy/server/navkwa-prod-01.md`.

### Cloudflare Production Rules

What: Cloudflare is the public edge in front of the VPS.

Why: it handles DNS, proxying, TLS at the edge, caching, and basic traffic protection.

Problem solved: the origin server is less exposed, TLS is easier to manage, and static traffic can be cached closer to visitors.

Recommended settings:

- DNS records proxied through Cloudflare where appropriate.
- `navkwa.com` points to `49.12.103.75`.
- `www.navkwa.com` aliases `navkwa.com`.
- Future `build.navkwa.com`, `api.navkwa.com`, and `console.navkwa.com` point to `49.12.103.75` when ready.
- SSL/TLS mode set to Full (strict).
- Origin certificate installed on Nginx.
- HTTP to HTTPS redirects enabled at Nginx or Cloudflare.
- Real visitor IP restored in Nginx using `CF-Connecting-IP`.

Recommended DNS records:

| Type | Name | Content | Proxy |
| --- | --- | --- | --- |
| A | @ | 49.12.103.75 | Proxied |
| CNAME | www | navkwa.com | Proxied |
| A | build | 49.12.103.75 | Proxied when ready |
| A | api | 49.12.103.75 | Proxied when ready |
| A | console | 49.12.103.75 | Proxied when ready |

Verify:

```bash
curl -I https://navkwa.com
sudo tail -f /var/log/nginx/access.log
```

Troubleshoot:

- If Nginx logs only Cloudflare IPs, configure `deploy/nginx/cloudflare-real-ip.conf.example`.
- If Cloudflare shows 525 or 526, check the origin certificate and SSL/TLS mode.

## Phase 2: Security

Command companion for this server: `deploy/server/phase-2-security-commands.md`.

### UFW Firewall

What: UFW is Ubuntu's simple firewall manager.

Why: It decides which network ports are reachable from the internet.

Problem solved: without a firewall, accidental services can become public. In production, only SSH, HTTP, and HTTPS should normally be open.

Install:

```bash
sudo apt update
sudo apt install -y ufw
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow OpenSSH
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

Verify:

```bash
sudo ufw status verbose
```

Troubleshoot:

- If SSH stops working, use the VPS provider console and confirm `OpenSSH` or your custom SSH port is allowed.
- If the website does not load, confirm ports `80/tcp` and `443/tcp` are allowed.

### SSH Hardening

What: SSH hardening limits how administrators can access the server.

Why: SSH is the front door for server administration.

Problem solved: password brute force, root login abuse, and broad administrator access.

Recommended settings in `/etc/ssh/sshd_config.d/navkwa.conf`:

```text
PermitRootLogin no
PasswordAuthentication no
PubkeyAuthentication yes
MaxAuthTries 3
LoginGraceTime 30
AllowUsers deploy
```

Apply:

```bash
sudo sshd -t
sudo systemctl reload ssh
```

Verify:

```bash
ssh deploy@your-server-ip
sudo journalctl -u ssh --since "10 minutes ago"
```

Troubleshoot:

- Keep one existing SSH session open while testing a new session.
- If config validation fails, `sudo sshd -t` will show the broken line.

### Automatic Security Updates

What: unattended upgrades install Ubuntu security patches automatically.

Why: production servers must receive critical security updates quickly.

Problem solved: known vulnerabilities staying open because manual patching was forgotten.

Install:

```bash
sudo apt install -y unattended-upgrades
sudo dpkg-reconfigure --priority=low unattended-upgrades
```

Verify:

```bash
systemctl status unattended-upgrades
sudo unattended-upgrade --dry-run --debug
```

Troubleshoot:

- Check `/var/log/unattended-upgrades/`.
- If packages are held back, run `sudo apt update && sudo apt list --upgradable`.

### Fail2Ban

What: Fail2Ban watches logs and bans IP addresses that repeatedly fail authentication or attack common endpoints.

Why: it slows brute-force attempts before they become noisy or expensive.

Problem solved: repeated SSH and web abuse from the same IP.

Install:

```bash
sudo apt install -y fail2ban
sudo cp deploy/fail2ban/jail.local.example /etc/fail2ban/jail.local
sudo systemctl enable --now fail2ban
```

Verify:

```bash
sudo fail2ban-client status
sudo fail2ban-client status sshd
```

Troubleshoot:

- Check `/var/log/fail2ban.log`.
- If a trusted IP is banned, use `sudo fail2ban-client set sshd unbanip IP_ADDRESS`.

### Time Synchronization

What: systemd-timesyncd keeps server time correct.

Why: SSL certificates, logs, payment callbacks, queues, and scheduled jobs depend on accurate time.

Problem solved: confusing logs, expired certificate errors, failed webhook signature checks, and missed scheduled jobs.

Verify:

```bash
timedatectl
```

Troubleshoot:

- Ensure `System clock synchronized: yes`.
- Restart with `sudo systemctl restart systemd-timesyncd`.

### Hostname Verification

What: the server hostname identifies the machine in logs and alerts.

Why: clear names matter once there are multiple Navkwa servers.

Problem solved: confusion during incident response and monitoring.

Set:

```bash
sudo hostnamectl set-hostname navkwa-prod-01
hostnamectl
```

Verify:

```bash
hostname
hostname -f
```

Troubleshoot:

- Check `/etc/hosts` if `hostname -f` is wrong.

## Phase 3: Runtime

### PHP and PHP-FPM

What: PHP runs Laravel; PHP-FPM is the long-running process manager used by Nginx.

Why: Laravel needs PHP, and Nginx passes PHP requests to PHP-FPM.

Problem solved: fast, stable PHP execution without using PHP's local development server.

Install on Ubuntu 24.04:

```bash
sudo apt install -y php8.3-fpm php8.3-cli php8.3-pgsql php8.3-redis php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-bcmath php8.3-intl
```

Verify:

```bash
php -v
systemctl status php8.3-fpm
php -m | grep -E "pgsql|redis|mbstring|curl|xml"
```

Troubleshoot:

- PHP-FPM logs: `/var/log/php8.3-fpm.log`.
- Nginx PHP errors often mean the `fastcgi_pass` socket path is wrong.

### Composer

What: Composer installs PHP dependencies.

Why: Laravel and its packages are managed through Composer.

Problem solved: reproducible PHP dependencies from `composer.lock`.

Verify:

```bash
composer --version
composer validate
```

Troubleshoot:

- If Composer runs out of memory, check server RAM and swap.
- In production, use `composer install --no-dev --prefer-dist --optimize-autoloader`.

### Node.js and npm

What: Node.js and npm build frontend assets when an application has a `package.json`.

Why: many Laravel apps use Vite, Tailwind, or bundlers for production CSS and JavaScript.

Problem solved: production assets are built on the server or CI runner instead of manually copied.

Current website note: this repository does not currently have a `package.json`, so the deploy script skips Node builds for the website. Navkwa Build may need Node if its frontend has a build pipeline.

Verify:

```bash
node -v
npm -v
```

Troubleshoot:

- If `npm ci` fails, confirm `package-lock.json` is committed.
- If assets are missing, confirm `npm run build` ran and Nginx points to `current/public`.

### PostgreSQL

What: PostgreSQL is the production SQL database.

Why: it stores users, enquiries, payments, content, chat messages, audit logs, and ERP data.

Problem solved: reliable relational storage with backups, indexing, and transactional safety.

Install:

```bash
sudo apt install -y postgresql postgresql-contrib
```

Create one database per app:

```bash
sudo -u postgres createuser navkwa_website_user --pwprompt
sudo -u postgres createdb navkwa_website --owner=navkwa_website_user
```

Verify:

```bash
sudo -u postgres psql -c "\l"
php artisan migrate:status
```

Troubleshoot:

- PostgreSQL logs: `/var/log/postgresql/`.
- Laravel connection errors usually mean `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `DB_HOST`, or `DB_PORT` is wrong.

### Redis

What: Redis is fast in-memory storage for cache, sessions, queues, and locks.

Why: it takes short-lived workload away from PostgreSQL.

Problem solved: faster sessions/cache and more efficient queue processing.

Install:

```bash
sudo apt install -y redis-server
sudo systemctl enable --now redis-server
```

Verify:

```bash
redis-cli ping
php -m | grep redis
```

Troubleshoot:

- Redis logs: `/var/log/redis/redis-server.log`.
- If Laravel cannot use Redis, confirm the PHP Redis extension is installed.

### Supervisor

What: Supervisor keeps queue workers running.

Why: Laravel queue workers are long-running processes and must restart if they crash.

Problem solved: queued jobs stop processing after a deploy, reboot, or crash.

Install:

```bash
sudo apt install -y supervisor
sudo cp deploy/supervisor/navkwa-website-worker.conf.example /etc/supervisor/conf.d/navkwa-website-worker.conf
sudo supervisorctl reread
sudo supervisorctl update
```

Verify:

```bash
sudo supervisorctl status
```

Troubleshoot:

- Worker logs: `/var/www/navkwa-website/shared/logs/queue-worker.log`.
- Restart workers with `sudo supervisorctl restart navkwa-website-worker:*`.

## Phase 4: Web Layer

### Nginx

What: Nginx receives browser traffic and serves public files.

Why: it is the production web server in front of Laravel.

Problem solved: routing, static file delivery, TLS termination, compression, and request limits.

Install:

```bash
sudo apt install -y nginx
sudo cp deploy/nginx/navkwa-website.conf.example /etc/nginx/sites-available/navkwa-website
sudo ln -s /etc/nginx/sites-available/navkwa-website /etc/nginx/sites-enabled/navkwa-website
sudo nginx -t
sudo systemctl reload nginx
```

Verify:

```bash
curl -I https://your-domain.com
curl https://your-domain.com/health
```

Troubleshoot:

- Nginx config test: `sudo nginx -t`.
- Access log: `/var/log/nginx/access.log`.
- Error log: `/var/log/nginx/error.log`.

### HTTP to HTTPS

What: redirect all HTTP traffic to HTTPS.

Why: production traffic must be encrypted.

Problem solved: cookies, logins, payment callbacks, and admin sessions being sent over plaintext HTTP.

Verify:

```bash
curl -I http://your-domain.com
```

Expected: a `301` redirect to `https://your-domain.com`.

### Gzip and Brotli

What: response compression for text assets.

Why: compressed CSS, JavaScript, HTML, and JSON load faster.

Problem solved: slow page loads and unnecessary bandwidth.

Verify:

```bash
curl -H "Accept-Encoding: gzip" -I https://your-domain.com
```

Troubleshoot:

- Confirm compression is enabled in Nginx.
- Avoid compressing already-compressed assets like images and videos.

### Caching Headers

What: browser cache rules for static assets.

Why: images, CSS, JS, and videos should not be downloaded repeatedly.

Problem solved: slow repeat visits and high bandwidth use.

Verify:

```bash
curl -I https://your-domain.com/assets/css/styles.css
```

Troubleshoot:

- If new assets do not show, cache-bust the filename or clear CDN cache in Cloudflare.

## Phase 5: Applications

Production layout:

```text
/var/www/
  navkwa-website/
    current -> releases/20260814_001/
    releases/
    shared/
      .env
      storage/
      logs/
  navkwa-build/
    current -> releases/20260814_001/
    releases/
    shared/
      .env
      storage/
      logs/
```

What: each deploy creates a new immutable release folder and atomically points `current` to it.

Why: releases make rollbacks and clean deployments possible.

Problem solved: failed deployments overwriting the live app.

Verify:

```bash
ls -la /var/www/navkwa-website
readlink /var/www/navkwa-website/current
```

Troubleshoot:

- If a release fails, do not switch `current`.
- To roll back, point `current` to the previous release and reload PHP-FPM/Nginx.

### Environment Variables

What: `.env` contains production secrets and server-specific settings.

Why: secrets must stay outside Git and outside release folders.

Problem solved: accidental credential leaks and per-release config drift.

Use:

```bash
cp .env.production.example /var/www/navkwa-website/shared/.env
nano /var/www/navkwa-website/shared/.env
```

Verify:

```bash
php artisan config:show app
```

Troubleshoot:

- After editing `.env`, run `php artisan config:cache` from the current release.

### Storage and Permissions

What: Laravel needs writable storage for logs, cache files, sessions when not using Redis, and public uploads.

Why: releases are replaced; storage must persist.

Problem solved: uploads and logs disappearing after deployment.

Set:

```bash
sudo chown -R www-data:www-data /var/www/navkwa-website/shared/storage /var/www/navkwa-website/shared/logs
sudo chmod -R ug+rwX /var/www/navkwa-website/shared/storage /var/www/navkwa-website/shared/logs
```

Verify:

```bash
sudo -u www-data test -w /var/www/navkwa-website/shared/storage && echo writable
```

Troubleshoot:

- Laravel `Permission denied` errors usually point to `storage/` or `bootstrap/cache`.

## Phase 6: Production Operations

### SSL

What: SSL/TLS encrypts traffic between visitors, Cloudflare, and the server.

Why: admin logins, payment callbacks, forms, and sessions require HTTPS.

Recommended Cloudflare mode: Full (strict).

Verify:

```bash
curl -I https://your-domain.com
```

Troubleshoot:

- Cloudflare 525/526 errors usually mean the origin certificate is missing, expired, or invalid.

### Queue Workers

What: queue workers process background jobs.

Why: slow or retryable tasks should not block web requests.

Problem solved: timeouts and poor user experience when emails, notifications, imports, or callbacks are slow.

Verify:

```bash
sudo supervisorctl status
php artisan queue:failed
```

Troubleshoot:

- Restart after deploy with `php artisan queue:restart`.
- Check failed jobs with `php artisan queue:failed`.

### Laravel Scheduler

What: the scheduler runs Laravel scheduled commands every minute through cron.

Why: Laravel centralizes recurring jobs in code.

Problem solved: scattered system cron commands that are hard to audit.

Install:

```bash
sudo cp deploy/cron/navkwa-website-scheduler.example /etc/cron.d/navkwa-website-scheduler
```

Verify:

```bash
sudo grep CRON /var/log/syslog | tail
```

Troubleshoot:

- Confirm the cron file has a trailing newline.
- Confirm the path points to `/var/www/navkwa-website/current`.

### Log Rotation

What: logrotate compresses and removes old logs.

Why: logs grow forever unless managed.

Problem solved: full disks causing downtime.

Install:

```bash
sudo cp deploy/logrotate/navkwa-website.example /etc/logrotate.d/navkwa-website
sudo logrotate -d /etc/logrotate.d/navkwa-website
```

Verify:

```bash
sudo logrotate -d /etc/logrotate.d/navkwa-website
```

Troubleshoot:

- If rotation fails, check file ownership and paths.

### Health Checks

What: a small route returns whether the app is reachable.

Why: monitoring needs a lightweight endpoint.

Problem solved: knowing whether Nginx, PHP-FPM, and Laravel can serve a request.

Verify:

```bash
curl https://your-domain.com/health
```

Expected:

```json
{"status":"ok","service":"navkwa-website"}
```

### Monitoring

What: monitoring tracks uptime, disk space, CPU, memory, queues, and errors.

Why: problems should alert us before users report them.

Problem solved: silent failures.

Minimum checks:

- HTTPS uptime on `/health`.
- Disk usage under 80 percent.
- Supervisor workers running.
- Failed jobs count.
- SSL certificate expiry.
- Database backups completing.

### Backups

What: backups protect PostgreSQL data, `.env`, storage, and uploaded files.

Why: production data is the business record.

Problem solved: data loss from mistakes, corruption, or server failure.

Minimum backup command:

```bash
pg_dump navkwa_website | gzip > /var/backups/navkwa_website_$(date +%F).sql.gz
```

Verify:

```bash
gzip -t /var/backups/navkwa_website_YYYY-MM-DD.sql.gz
```

Troubleshoot:

- A backup that has never been restored is only a hope. Test restore on a separate database.

## Phase 7: CI/CD

Target workflow:

```text
MacBook
  -> git push
  -> GitHub
  -> GitHub Actions
  -> Production Server
  -> zero-downtime deployment
```

Production rule: no FTP, no cPanel uploads, no ZIP-copy deployments.

Future GitHub Actions should:

- Run tests.
- Build assets if `package.json` exists.
- SSH to the production server using a deploy key.
- Run `deploy/scripts/release-deploy.sh`.
- Keep shared `.env` and `storage/` outside Git.
- Restart queue workers gracefully.
- Leave the previous release available for rollback.

Recommended server setup for CI/CD:

```bash
cp deploy/scripts/release-deploy.sh /var/www/navkwa-website/shared/release-deploy.sh
cp deploy/scripts/rollback.sh /var/www/navkwa-website/shared/rollback.sh
chmod +x /var/www/navkwa-website/shared/release-deploy.sh /var/www/navkwa-website/shared/rollback.sh
```

The inactive template at `.github/workflows/deploy-production.yml.example` shows the intended GitHub Actions shape. Rename it to `.github/workflows/deploy-production.yml` only after the production SSH secrets are configured.

## Deployment Readiness Checklist

Before first production deployment:

- Cloudflare DNS points to the VPS.
- Cloudflare SSL mode is Full (strict).
- UFW allows SSH, 80, and 443 only.
- SSH password login is disabled.
- Fail2Ban is running.
- Server time is synchronized.
- PHP-FPM, PostgreSQL, Redis, Nginx, and Supervisor are installed.
- `/var/www/navkwa-website/shared/.env` exists and contains production values.
- PostgreSQL database and user exist.
- Redis responds to `PING`.
- Nginx vhost points to `/var/www/navkwa-website/current/public`.
- `/health` returns JSON.
- Queue workers are running.
- Scheduler cron is installed.
- Backups are configured and tested.
