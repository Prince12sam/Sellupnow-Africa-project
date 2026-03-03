# SellUpNow — Production VPS Installation Guide

> **Platform:** Ubuntu 22.04 LTS (recommended) · Nginx · PHP 8.2 · MySQL 8.0  
> **Two apps to deploy:**  
> - **Frontend** (`main-file/listocean/core/`) — customer-facing web at your main domain  
> - **Admin panel** (`sellupnow-admin/`) — admin interface at a separate subdomain or port  
> **Two databases:** `listocean_db` (all real data) · `sellupnow_admin` (admin app config)

---

## Table of Contents

1. [Server Requirements](#1-server-requirements)
2. [Install System Dependencies](#2-install-system-dependencies)
3. [Upload the Codebase](#3-upload-the-codebase)
4. [Configure Both Apps](#4-configure-both-apps)
5. [Install Dependencies](#5-install-dependencies)
6. [Run the Web Installer (Database Setup)](#6-run-the-web-installer-database-setup)
7. [Post-Install Steps](#7-post-install-steps)
8. [Nginx Configuration](#8-nginx-configuration)
9. [HTTPS with Let's Encrypt](#9-https-with-lets-encrypt)
10. [Queue Worker (Supervisor)](#10-queue-worker-supervisor)
11. [Scheduler (Cron)](#11-scheduler-cron)
12. [File Storage & Permissions](#12-file-storage--permissions)
13. [Admin Panel First-Login Setup](#13-admin-panel-first-login-setup)
14. [Smoke Tests](#14-smoke-tests)
15. [Security Checklist](#15-security-checklist)

---

## 1. Server Requirements

| Component | Minimum | Recommended |
|-----------|---------|-------------|
| OS | Ubuntu 20.04 | Ubuntu 22.04 LTS |
| CPU | 2 vCPU | 4 vCPU |
| RAM | 2 GB | 4 GB |
| Disk | 20 GB SSD | 40 GB SSD |
| PHP | 8.2 | 8.2+ |
| MySQL | 8.0 | 8.0 |
| Nginx | 1.18+ | latest stable |
| Composer | 2.x | latest |
| Node.js | 18.x | 20 LTS |
| Supervisor | any | for queue workers |

**Required PHP extensions:**
`bcmath` `ctype` `curl` `dom` `fileinfo` `gd` `json` `mbstring` `openssl` `pcre` `pdo_mysql` `tokenizer` `xml` `zip`

---

## 2. Install System Dependencies

```bash
# Update packages
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2 and required extensions
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-bcmath php8.2-curl \
  php8.2-dom php8.2-fileinfo php8.2-gd php8.2-mbstring php8.2-xml php8.2-zip \
  php8.2-tokenizer php8.2-intl php8.2-redis

# Install MySQL 8.0
sudo apt install -y mysql-server
sudo mysql_secure_installation

# Install Nginx
sudo apt install -y nginx

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js 20 LTS
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Install Supervisor (for queue workers)
sudo apt install -y supervisor

# Install Certbot (for SSL)
sudo apt install -y certbot python3-certbot-nginx
```

---

## 3. Upload the Codebase

### Option A — Git clone (recommended)

```bash
cd /var/www
git clone https://github.com/Prince12sam/Sellupnow-Africa-project.git sellupnow
cd sellupnow
```

### Option B — SFTP upload

Upload the entire project folder to `/var/www/sellupnow/`.

After upload, set correct ownership:

```bash
sudo chown -R www-data:www-data /var/www/sellupnow
sudo chmod -R 755 /var/www/sellupnow
```

---

## 4. Configure Both Apps

### 4.1 MySQL — Create Databases and User

```sql
-- Login as root
sudo mysql

-- Create both databases
CREATE DATABASE listocean_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE sellupnow_admin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create a dedicated user (replace 'yourpassword' with a strong password)
CREATE USER 'sellupnow'@'localhost' IDENTIFIED BY 'yourpassword';
GRANT ALL PRIVILEGES ON listocean_db.* TO 'sellupnow'@'localhost';
GRANT ALL PRIVILEGES ON sellupnow_admin.* TO 'sellupnow'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

### 4.2 Frontend — `.env` File

```bash
cd /var/www/sellupnow/main-file/listocean/core
cp .env.example .env
nano .env
```

Set these values:

```ini
APP_NAME=SellUpNow
APP_ENV=production
APP_KEY=                        # leave blank — filled by: php artisan key:generate
APP_DEBUG=false
APP_URL=https://yourdomain.com  # your actual domain

LOG_CHANNEL=daily
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=listocean_db
DB_USERNAME=sellupnow
DB_PASSWORD=yourpassword

# Queue & Cache (use database or redis in production)
QUEUE_CONNECTION=database
CACHE_DRIVER=file
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Filesystem
FILESYSTEM_DISK=public

# SMTP is NOT configured here — admin panel controls it via DB
# CustomConfigServiceProvider reads from sellupnow_admin.settings at runtime

# Web installer — enable ONLY during initial setup
INSTALLER_ENABLED=true
```

---

### 4.3 Admin Panel — `.env` File

```bash
cd /var/www/sellupnow/sellupnow-admin
cp .env.example .env
nano .env
```

Set these values:

```ini
APP_NAME=SellUpNow
APP_ENV=production
APP_KEY=                        # leave blank — filled by: php artisan key:generate
APP_DEBUG=false
APP_URL=https://admin.yourdomain.com   # admin subdomain

LOG_CHANNEL=daily
LOG_LEVEL=error

# Admin panel's own database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sellupnow_admin
DB_USERNAME=sellupnow
DB_PASSWORD=yourpassword

# Secondary connection — admin bridges to listocean frontend DB
LISTOCEAN_DB_HOST=127.0.0.1
LISTOCEAN_DB_PORT=3306
LISTOCEAN_DB_DATABASE=listocean_db
LISTOCEAN_DB_USERNAME=sellupnow
LISTOCEAN_DB_PASSWORD=yourpassword

# URL of customer web (used by admin to build document preview URLs)
CUSTOMER_WEB_URL=https://yourdomain.com

# Queue & Cache
QUEUE_CONNECTION=database
CACHE_DRIVER=file
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Mail FROM (admin sets the full SMTP config via the admin panel UI)
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME=SellUpNow

# Web installer — enable ONLY during initial setup
INSTALLER_ENABLED=true

# Cross-app API key (admin uploads badges to frontend)
LISTOCEAN_ADMIN_API_KEY=replace_with_a_long_random_secret_key
```

---

## 5. Install Dependencies

Run for **both** apps:

> **Private packages note:** The three Xgenious private packages (`installer`, `paymentgateway`, `xgapiclient`) are bundled directly in the repo under `main-file/listocean/core/packages/`. Composer loads them from there with no GitHub token required. Do **not** run `composer install` from the original Codecanyon zip — always use this repo.

> **Important — do NOT run `php artisan config:cache` until after the web installer completes.** Running `config:cache` before installation causes `env()` to return null, which makes the installer think the app is not configured and redirects all traffic to `/install`. Run the caches in Step 7 (Post-Install) only.

```bash
# ── Frontend ──────────────────────────────────────────────────────────────
cd /var/www/sellupnow/main-file/listocean/core

composer install --no-dev --optimize-autoloader
php artisan key:generate
npm install && npm run build

# ── Admin Panel ───────────────────────────────────────────────────────────
cd /var/www/sellupnow/sellupnow-admin

composer install --no-dev --optimize-autoloader
php artisan key:generate
npm install && npm run build
```

---

## 6. Run the Web Installer (Database Setup)

The platform ships with `joynala/web-installer` — a web wizard that creates all tables and seeds the required data.

> `INSTALLER_ENABLED=true` must be set in **both** `.env` files before this step.

> **Note on purchase verification:** Both installers have had the external purchase-verification API calls disabled. The original flow contacted the Xgenious/Joynala license servers which:
> - For the **frontend**: downloaded and imported a SQL dump that replaced the database with original Codecanyon demo data
> - For the **admin**: returned "restore" items that overwrote custom PHP source files with original Codecanyon code
>
> The frontend installer now runs `php artisan migrate:fresh --seed` (our own migrations + seeders). The admin installer skips the verify step entirely. **Do not enter a purchase code when prompted — just click Next/Skip.**

### 6.1 Run the Admin installer first

Open in your browser:
```
https://admin.yourdomain.com/install
```

Follow the wizard steps:
1. **Welcome** — click Next
2. **Server Requirements** — all items must be green
3. **Folder Permissions** — all items must be green
4. **Database Configuration** — enter: host `127.0.0.1`, database `sellupnow_admin`, user `sellupnow`, password
5. **Application Setup** — fill app name (`SellUpNow Admin`), admin email, admin password, app URL (`https://admin.yourdomain.com`)
6. **Purchase Verification** — this step is disabled; if shown, click Skip or Next without entering a code
7. **Installation** — wizard runs `migrate:fresh --seed` — creates all tables and seeds roles/permissions
8. **Finish** — confirm you see the success screen

### 6.2 Run the Frontend installer

Open in your browser:
```
https://yourdomain.com/install
```

Follow the same steps, using database `listocean_db` and frontend URL (`https://yourdomain.com`).

The frontend installer runs `php artisan migrate:fresh --seed` which creates all tables and seeds:
- 4 admin roles (Super Admin, Admin, Editor, Manager) with 212 permissions
- English (UK) default language

After completion the installer calls `create_admin()` to insert the admin user you specified into the `admins` table.

### 6.3 Disable the installer immediately after both complete

```bash
# Frontend
sed -i 's/INSTALLER_ENABLED=true/INSTALLER_ENABLED=false/' \
  /var/www/sellupnow/main-file/listocean/core/.env

# Admin
sed -i 's/INSTALLER_ENABLED=true/INSTALLER_ENABLED=false/' \
  /var/www/sellupnow/sellupnow-admin/.env
```

Verify they are set to `false` before continuing:

```bash
grep INSTALLER_ENABLED /var/www/sellupnow/main-file/listocean/core/.env
grep INSTALLER_ENABLED /var/www/sellupnow/sellupnow-admin/.env
```

---

## 7. Post-Install Steps

Run for **both** apps:

```bash
# ── Frontend ──────────────────────────────────────────────────────────────
cd /var/www/sellupnow/main-file/listocean/core

# Run any pending migrations (in addition to installer)
php artisan migrate --force

# Create the public storage symlink (for uploaded images/files)
php artisan storage:link

# Seed required location data for Ghana
php artisan listocean:import-locations --country="Ghana"

# Optimise for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Create the sessions and jobs tables (needed if QUEUE_CONNECTION=database)
php artisan queue:table
php artisan session:table
php artisan migrate --force


# ── Admin Panel ───────────────────────────────────────────────────────────
cd /var/www/sellupnow/sellupnow-admin

php artisan migrate --force
php artisan storage:link

php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan queue:table
php artisan session:table
php artisan migrate --force
```

---

## 8. Nginx Configuration

### 8.1 Frontend virtual host

```bash
sudo nano /etc/nginx/sites-available/sellupnow-frontend
```

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;

    root /var/www/sellupnow/main-file/listocean/core/public;
    index index.php;

    client_max_body_size 50M;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 8.2 Admin panel virtual host

```bash
sudo nano /etc/nginx/sites-available/sellupnow-admin
```

```nginx
server {
    listen 80;
    server_name admin.yourdomain.com;

    root /var/www/sellupnow/sellupnow-admin/public;
    index index.php;

    client_max_body_size 50M;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 8.3 Enable both sites and reload

```bash
sudo ln -s /etc/nginx/sites-available/sellupnow-frontend /etc/nginx/sites-enabled/
sudo ln -s /etc/nginx/sites-available/sellupnow-admin    /etc/nginx/sites-enabled/

sudo nginx -t && sudo systemctl reload nginx
```

---

## 9. HTTPS with Let's Encrypt

```bash
# Obtain certificates for both domains
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
sudo certbot --nginx -d admin.yourdomain.com

# Certbot modifies the Nginx config automatically and sets up auto-renewal
# Verify auto-renewal works
sudo certbot renew --dry-run
```

After Certbot runs, update your `.env` files to use `https://`:

```bash
# Frontend
sed -i 's|APP_URL=http://|APP_URL=https://|' \
  /var/www/sellupnow/main-file/listocean/core/.env

# Admin
sed -i 's|APP_URL=http://|APP_URL=https://|' \
  /var/www/sellupnow/sellupnow-admin/.env
sed -i 's|CUSTOMER_WEB_URL=http://|CUSTOMER_WEB_URL=https://|' \
  /var/www/sellupnow/sellupnow-admin/.env
```

Then re-cache config for both apps:

```bash
cd /var/www/sellupnow/main-file/listocean/core && php artisan config:cache
cd /var/www/sellupnow/sellupnow-admin && php artisan config:cache
```

---

## 10. Queue Worker (Supervisor)

The queue handles async jobs: email notifications, wallet events, escrow timers, membership expiry.

### 10.1 Frontend queue worker

```bash
sudo nano /etc/supervisor/conf.d/sellupnow-frontend-worker.conf
```

```ini
[program:sellupnow-frontend-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/sellupnow/main-file/listocean/core/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/sellupnow/main-file/listocean/core/storage/logs/worker.log
stopwaitsecs=3600
```

### 10.2 Admin panel queue worker

```bash
sudo nano /etc/supervisor/conf.d/sellupnow-admin-worker.conf
```

```ini
[program:sellupnow-admin-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/sellupnow/sellupnow-admin/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/sellupnow/sellupnow-admin/storage/logs/worker.log
stopwaitsecs=3600
```

### 10.3 Start the workers

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start all
sudo supervisorctl status
```

---

## 11. Scheduler (Cron)

The Laravel scheduler drives: membership expiry, featured ad expiry, boost expiry, escrow auto-release, and sitemap generation.

Add one cron entry that runs the scheduler for **both** apps every minute:

```bash
sudo crontab -e -u www-data
```

Add these two lines:

```cron
* * * * * cd /var/www/sellupnow/main-file/listocean/core && php artisan schedule:run >> /dev/null 2>&1
* * * * * cd /var/www/sellupnow/sellupnow-admin && php artisan schedule:run >> /dev/null 2>&1
```

**Scheduled jobs that will run automatically:**

| Job | Schedule | What it does |
|-----|----------|-------------|
| `ExpireMemberships` | Daily 00:05 | Sets `user_memberships.status = expired` |
| `DowngradeExpiredUsers` | Daily 00:10 | Deactivates excess listings for expired free users |
| `ExpireFeaturedAds` | Hourly | Deactivates expired featured ad activations |
| `ExpireBoosts` | Hourly | Marks expired boost records |
| `EscrowAutoRelease` | Hourly | Auto-releases escrow after buyer confirm deadline |
| `GenerateSitemap` | Weekly (Sun 02:00) | Rebuilds `public/sitemap.xml` |

---

## 12. File Storage & Permissions

```bash
# Set correct ownership for both apps
sudo chown -R www-data:www-data /var/www/sellupnow
sudo chmod -R 755 /var/www/sellupnow

# Storage and bootstrap/cache must be writable
sudo chmod -R 775 /var/www/sellupnow/main-file/listocean/core/storage
sudo chmod -R 775 /var/www/sellupnow/main-file/listocean/core/bootstrap/cache
sudo chmod -R 775 /var/www/sellupnow/sellupnow-admin/storage
sudo chmod -R 775 /var/www/sellupnow/sellupnow-admin/bootstrap/cache

# Verify the public storage symlinks exist
ls -la /var/www/sellupnow/main-file/listocean/core/public/storage
ls -la /var/www/sellupnow/sellupnow-admin/public/storage
# Both should show: storage -> ../storage/app/public
```

---

## 13. Admin Panel First-Login Setup

Open `https://admin.yourdomain.com/admin` in your browser and log in with the credentials you set during the web installer.

Complete these admin setup steps **in order** before going live:

### Step 1 — Configure SMTP Mail (required for transactional emails)
`Admin → Settings → Mail Configuration`
- Set SMTP Host, Port, Username, Password
- Set Encryption: TLS
- Send a test email to confirm delivery

### Step 2 — Configure Payment Gateway (required for wallet top-up)
`Admin → Settings → Payment Gateways`
- Select Paystack
- Enter your Paystack **Live** Secret Key and Public Key
- Ensure currency is set to **GHS**

### Step 3 — Set Site Name, Logo, and Basic Settings
`Admin → Settings → General Settings`
- Upload logo, favicon
- Set App Name, contact email, support phone

### Step 4 — Create the 4 Membership Plans
`Admin → Membership Plans → Create`

Follow the plan setup in `MEMBERSHIP-SYSTEM.md` §6 exactly. Create in this order so IDs are predictable:

| Order | Plan | Price | Duration Days |
|-------|------|-------|---------------|
| 1 | Free | ₵0 | 36500 |
| 2 | Starter | ₵49 | 30 |
| 3 | Pro | ₵149 | 30 |
| 4 | Business | ₵399 | 30 |

### Step 5 — Create Featured Ad Packages
`Admin → Featured Ad Packages → Create`

| Name | Price | Duration |
|------|-------|----------|
| Bronze Feature | ₵40 | 7 days |
| Silver Feature | ₵70 | 14 days |
| Gold Feature | ₵130 | 30 days |

### Step 6 — Import Location Data (Ghana)

```bash
cd /var/www/sellupnow/sellupnow-admin
php artisan listocean:import-locations --country="Ghana"
```

This populates countries → states → cities dropdowns on the listing form.

### Step 7 — Configure Firebase Push Notifications
`Admin → Settings → Firebase`
- Paste your Firebase Server Key (from Firebase Console → Project Settings → Cloud Messaging)

### Step 8 — Set Escrow Commission
`Admin → Escrow → Settings`
- Set commission percentage (e.g. 5%)
- Set escrow auto-release days (e.g. 7)
- Set seller accept deadline days (e.g. 3)

---

## 14. Smoke Tests

Run these checks after setup:

```bash
# ── From server terminal ──────────────────────────────────────────────────

# Check both apps return 200/302
curl -o /dev/null -sw "%{http_code}" https://yourdomain.com
curl -o /dev/null -sw "%{http_code}" https://admin.yourdomain.com/admin

# Check queue workers are running
sudo supervisorctl status

# Check cron is registered
sudo crontab -l -u www-data

# Check scheduler itself
cd /var/www/sellupnow/main-file/listocean/core
php artisan schedule:list

# Check storage links are healthy
ls -la public/storage
```

**Browser checklist:**

| Test | Expected |
|------|----------|
| `https://yourdomain.com` | Homepage loads with listings |
| `https://yourdomain.com/login` | Login form renders |
| Register a new user account | Redirects to dashboard |
| Post a new listing | Listing saved with status=pending |
| `https://admin.yourdomain.com/admin` | Redirects to admin login |
| Admin login with installer credentials | Admin dashboard loads |
| Admin → Listing Moderation | Shows the pending listing |
| Approve the listing | Listing goes live on frontend |
| `https://yourdomain.com/user/wallet` | Wallet page shows ₵0.00 balance |
| `https://yourdomain.com/user/membership` | 4 membership tiers show |
| Admin → Mail Config → Send test email | Email delivered |

---

## 15. Security Checklist

Before announcing the site as live:

- [ ] `APP_ENV=production` in both `.env` files
- [ ] `APP_DEBUG=false` in both `.env` files
- [ ] `INSTALLER_ENABLED=false` in both `.env` files
- [ ] `QUEUE_CONNECTION=database` (not `sync`) in both `.env` files
- [ ] Both apps are behind HTTPS — no HTTP access in production
- [ ] MySQL user `sellupnow` only has access to `listocean_db` and `sellupnow_admin` — no `root` in production `.env`
- [ ] `/install` routes return 404 (verify in browser after disabling)
- [ ] `/update` routes return 404 permanently (already blocked by middleware)
- [ ] Nginx `deny all` rule for dotfiles is in place (already in config above)
- [ ] `storage/` and `bootstrap/cache/` are writable by `www-data` only
- [ ] No `.env` files are accessible via HTTP (Nginx config blocks them)
- [ ] Strong, unique `APP_KEY` in both apps (set by `php artisan key:generate`)
- [ ] Strong MySQL password (not `root`/blank)
- [ ] Supervisor workers running as `www-data`, not `root`
- [ ] Log rotation configured (Laravel uses `LOG_CHANNEL=daily`)
- [ ] Firewall: only ports 80, 443, and 22 (SSH) are open

### Optional but recommended
- [ ] Redis for cache + session + queue (replace `file`/`database` drivers)
- [ ] Fail2Ban on SSH
- [ ] Automated daily MySQL backups
- [ ] Server monitoring (UptimeRobot or similar) pointing at `https://yourdomain.com`

---

## Quick Reference — Useful Commands

```bash
# Re-cache after any .env or config change
php artisan config:cache && php artisan route:cache && php artisan view:cache

# Clear all caches
php artisan config:clear && php artisan cache:clear && php artisan view:clear && php artisan route:clear

# Run pending migrations
php artisan migrate --force

# Restart queue workers after code deploy
sudo supervisorctl restart all

# Check application logs
tail -f /var/www/sellupnow/main-file/listocean/core/storage/logs/laravel.log
tail -f /var/www/sellupnow/sellupnow-admin/storage/logs/laravel.log

# Rebuild frontend assets after code update
cd /var/www/sellupnow/main-file/listocean/core && npm run build
cd /var/www/sellupnow/sellupnow-admin && npm run build

# Check PHP-FPM status
sudo systemctl status php8.2-fpm

# Reload Nginx after config changes
sudo nginx -t && sudo systemctl reload nginx
```
