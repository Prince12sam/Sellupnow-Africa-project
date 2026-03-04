# SellUpNow — Production VPS Installation Guide

> **Last updated:** March 2026  
> **Target OS:** Ubuntu 22.04 LTS  
> **Architecture:** Single domain · Nginx · PHP-FPM · MySQL 8.0

---

## Table of Contents

1. [Platform Overview & Tech Stack](#1-platform-overview--tech-stack)
2. [Server Requirements](#2-server-requirements)
3. [Install System Dependencies](#3-install-system-dependencies)
4. [Upload the Codebase](#4-upload-the-codebase)
5. [Create Databases](#5-create-databases)
6. [Configure Both Apps (.env Files)](#6-configure-both-apps-env-files)
7. [Install PHP & JS Dependencies](#7-install-php--js-dependencies)
8. [Run the Web Installer (Database Setup)](#8-run-the-web-installer-database-setup)
9. [Post-Install Artisan Commands](#9-post-install-artisan-commands)
10. [Nginx Configuration](#10-nginx-configuration)
11. [HTTPS with Let's Encrypt](#11-https-with-lets-encrypt)
12. [Queue Workers (Supervisor)](#12-queue-workers-supervisor)
13. [Scheduler (Cron)](#13-scheduler-cron)
14. [File Permissions](#14-file-permissions)
15. [Admin Panel First-Login Setup](#15-admin-panel-first-login-setup)
16. [Smoke Tests](#16-smoke-tests)
17. [Security Checklist](#17-security-checklist)
18. [Deploying Code Updates](#18-deploying-code-updates)
19. [Quick Reference Commands](#19-quick-reference-commands)
20. [Troubleshooting](#20-troubleshooting)

---

## 1. Platform Overview & Tech Stack

SellUpNow Africa is a classified-ads and e-commerce marketplace platform built from two independent Laravel applications that share one MySQL server and run under a single domain.

### 1.1 Architecture at a Glance

```
yourdomain.com/               → Frontend (ListOcean) — customer-facing marketplace
yourdomain.com/admin          → Admin Panel (SellUpNow Admin) — operator back-office
yourdomain.com/build/         → Frontend Vite-compiled CSS/JS assets (served by Nginx)
yourdomain.com/admin-build/   → Admin Vite-compiled CSS/JS assets (served by Nginx)
yourdomain.com/storage/       → Frontend user uploads (listing images, profile photos)
yourdomain.com/admin/storage/ → Admin uploads (badges, logos)
```

Both apps are served by **one Nginx server block** and one SSL certificate. The admin panel reads and writes to the frontend's `listocean_db` through a secondary database connection named `listocean`.

### 1.2 Tech Stack

#### Servers & Infrastructure

| Technology | Version | Role |
|------------|---------|------|
| Ubuntu | 22.04 LTS | Operating system |
| Nginx | 1.18+ | Web server / reverse proxy |
| PHP | 8.2 | Runtime for both Laravel apps |
| PHP-FPM | 8.2 | FastCGI process manager |
| MySQL | 8.0 | Primary relational database |
| Supervisor | any | Keeps queue workers alive |
| Certbot | any | Manages SSL certificates (Let's Encrypt) |
| Redis | optional | Recommended for cache/session/queue in production |

#### Backend (both apps)

| Technology | Version | Role |
|------------|---------|------|
| Laravel | 10.x (frontend) / 11.x (admin) | MVC framework |
| Composer | 2.x | PHP dependency manager |
| nwidart/laravel-modules | — | Modular architecture for the frontend |
| joynala/web-installer | — | Browser-based setup wizard |
| spatie/laravel-permission | — | Role and permission management |
| Laravel Queue | — | Async jobs (emails, notifications, wallet events) |
| Laravel Scheduler | — | Cron-based jobs (membership expiry, sitemaps) |

#### Frontend App (Customer-Facing — ListOcean)

| Technology | Version | Role |
|------------|---------|------|
| Laravel Blade | — | Server-rendered HTML templates |
| Bootstrap | 5.x | CSS framework and grid system |
| SCSS | — | Custom styles compiled by Vite |
| Vite | 4.x | Asset bundler → outputs to `public/build/` |
| jQuery | — | DOM helpers and AJAX |
| Laravel Livewire | — | Reactive components (search filters, listing form) |
| Socket.IO / Pusher | — | Real-time chat and notifications |
| Google Maps JS SDK | — | Map view for listings |

#### Admin Panel (SellUpNow Admin)

| Technology | Version | Role |
|------------|---------|------|
| Vue 3 (Composition API) | 3.x | Admin dashboard SPA (all routes beyond login) |
| Vue Router | 4.x | Client-side routing within the admin SPA |
| Pinia | — | State management for Vue components |
| Tailwind CSS | 3.x | Utility-first CSS for admin UI |
| Vite | 5.x | Asset bundler → outputs to `public/admin-build/` |
| Axios | — | HTTP client for Vue → Laravel API calls |
| Laravel Blade | — | Outer shell (login page, app bootstrap) |

#### Mobile App

| Technology | Version | Role |
|------------|---------|------|
| Flutter | 3.x | Cross-platform mobile (Android & iOS) |
| Dart | 3.x | Programming language |
| Firebase | — | Push notifications and crash reporting |
| Google Sign-In | — | OAuth authentication |

#### Payment Gateways

| Gateway | Region | Status |
|---------|--------|--------|
| Paystack | West Africa / GHS | ✅ Primary |
| Stripe | Global | ✅ |
| Razorpay | India | ✅ |
| Cashfree | India | ✅ |
| Flutterwave | Africa | ✅ |

#### Databases

| Database | Used for |
|----------|---------|
| `listocean_db` | All customer data — users, listings, orders, wallets, chat, memberships, escrow |
| `sellupnow_admin` | Admin users, roles, permissions, settings, theme config, SMTP credentials |

---

## 2. Server Requirements

| Component | Minimum | Recommended |
|-----------|---------|-------------|
| OS | Ubuntu 20.04 | **Ubuntu 22.04 LTS** |
| CPU | 2 vCPU | 4 vCPU |
| RAM | 2 GB | **4 GB** |
| Disk | 20 GB SSD | 40 GB SSD |
| PHP | 8.2 | **8.2** |
| MySQL | 8.0 | **8.0** |
| Nginx | 1.18+ | latest stable |
| Composer | 2.x | latest |
| Node.js | 18.x | **20 LTS** |
| Supervisor | any | required |

**Required PHP extensions:**  
`bcmath` `ctype` `curl` `dom` `fileinfo` `gd` `json` `mbstring` `openssl` `pcre` `pdo_mysql` `tokenizer` `xml` `zip` `intl`

---

## 3. Install System Dependencies

Run all commands as a user with `sudo` access (do not run as `root`):

```bash
# Update package list
sudo apt update && sudo apt upgrade -y

# PHP 8.2 + all required extensions
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y \
  php8.2 php8.2-fpm php8.2-mysql php8.2-bcmath php8.2-curl \
  php8.2-dom php8.2-fileinfo php8.2-gd php8.2-mbstring \
  php8.2-xml php8.2-zip php8.2-tokenizer php8.2-intl

# Verify
php -v

# MySQL 8.0
sudo apt install -y mysql-server
sudo mysql_secure_installation
# Answer Y to all prompts; set a strong root password

# Nginx
sudo apt install -y nginx
sudo systemctl enable nginx
sudo systemctl start nginx

# Composer 2
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer --version

# Node.js 20 LTS
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
node -v && npm -v

# Supervisor (keeps queue workers alive)
sudo apt install -y supervisor
sudo systemctl enable supervisor

# Certbot (SSL certificates)
sudo apt install -y certbot python3-certbot-nginx
```

---

## 4. Upload the Codebase

### Option A — Git Clone (recommended)

```bash
cd /var/www
sudo git clone https://github.com/Prince12sam/Sellupnow-Africa-project.git sellupnow
cd sellupnow
```

### Option B — SFTP / SCP Upload

Upload the entire project folder to `/var/www/sellupnow/` using FileZilla, Cyberduck, or `scp`.

### Set Correct Ownership

Run this regardless of how you uploaded:

```bash
sudo chown -R www-data:www-data /var/www/sellupnow
sudo chmod -R 755 /var/www/sellupnow

# Allow your own user to run artisan/composer without sudo
sudo usermod -aG www-data $USER
# Log out and back in for this group change to take effect
```

---

## 5. Create Databases

```bash
# Log in to MySQL as root
sudo mysql
```

```sql
-- Create both application databases
CREATE DATABASE listocean_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

CREATE DATABASE sellupnow_admin
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- Create one dedicated DB user (replace the password with something strong)
CREATE USER 'sellupnow'@'localhost' IDENTIFIED BY 'StrongPassword123!';

-- Grant access to both databases
GRANT ALL PRIVILEGES ON listocean_db.*    TO 'sellupnow'@'localhost';
GRANT ALL PRIVILEGES ON sellupnow_admin.* TO 'sellupnow'@'localhost';

FLUSH PRIVILEGES;
EXIT;
```

> **Save that DB password** — you will need it in the `.env` files and the web installer wizard.

---

## 6. Configure Both Apps (.env Files)

> ⚠️ **Do NOT run `php artisan config:cache` until after the web installer has completed.**  
> Caching config before installation causes `env()` to return null, which sends all requests to `/install`.

### 6.1 Frontend `.env`

```bash
cd /var/www/sellupnow/main-file/listocean/core
cp .env.example .env
nano .env
```

```ini
APP_NAME="SellUpNow"
APP_ENV=production
APP_KEY=                          # leave blank — set by: php artisan key:generate
APP_DEBUG=false
APP_URL=https://yourdomain.com    # your actual domain — no trailing slash

LOG_CHANNEL=daily
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=listocean_db
DB_USERNAME=sellupnow
DB_PASSWORD=StrongPassword123!

QUEUE_CONNECTION=database
CACHE_DRIVER=file
SESSION_DRIVER=database
SESSION_LIFETIME=120

FILESYSTEM_DISK=public

# Enable installer ONLY during initial setup — disable immediately after
INSTALLER_ENABLED=true
```

> **Mail / SMTP note:** Do not configure mail credentials here. The frontend reads SMTP settings from the `sellupnow_admin` database at runtime via `CustomConfigServiceProvider`. Configure mail in the admin panel UI (Step 15, Step 1).

---

### 6.2 Admin Panel `.env`

```bash
cd /var/www/sellupnow/sellupnow-admin
cp .env.example .env
nano .env
```

```ini
APP_NAME="SellUpNow Admin"
APP_ENV=production
APP_KEY=                          # leave blank — set by: php artisan key:generate
APP_DEBUG=false
APP_URL=https://yourdomain.com    # same domain as frontend — admin routes use /admin prefix

LOG_CHANNEL=daily
LOG_LEVEL=error

# ── Admin panel's own database ────────────────────────────────────────────
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sellupnow_admin
DB_USERNAME=sellupnow
DB_PASSWORD=StrongPassword123!

# ── Secondary DB connection — admin reads/writes the frontend database ────
# Used for listing moderation, user management, order tracking, escrow, etc.
LISTOCEAN_DB_HOST=127.0.0.1
LISTOCEAN_DB_PORT=3306
LISTOCEAN_DB_DATABASE=listocean_db
LISTOCEAN_DB_USERNAME=sellupnow
LISTOCEAN_DB_PASSWORD=StrongPassword123!

# ── Cross-app URLs (same domain — single-domain architecture) ────────────
CUSTOMER_WEB_URL=https://yourdomain.com
LISTOCEAN_APP_URL=https://yourdomain.com
LISTOCEAN_API_BASE=https://yourdomain.com

# ── Cross-app file-system path — admin mirrors uploaded images to the frontend
# Must be the absolute server path to the frontend's public/ directory.
# Without this, hero images and media uploaded via admin will NOT appear on the frontend.
LISTOCEAN_PUBLIC_PATH=/var/www/sellupnow/main-file/listocean/core/public

# ── Cross-app API key (admin calls frontend badge-upload endpoint) ────────
# Generate a secure key with: openssl rand -hex 32
LISTOCEAN_ADMIN_API_KEY=replace_with_a_long_random_secret_key

QUEUE_CONNECTION=database
CACHE_DRIVER=file
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Mail FROM — full SMTP config is set in the admin panel UI after login
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="SellUpNow"

# Enable installer ONLY during initial setup — disable immediately after
INSTALLER_ENABLED=true
```

---

## 7. Install PHP & JS Dependencies

> **Private packages note:** The three Xgenious private packages (`installer`, `paymentgateway`, `xgapiclient`) are bundled directly in this repo under `main-file/listocean/core/packages/`. Composer loads them from there — no GitHub token or purchase code needed. Always use this repo, not the original Codecanyon zip.

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

**What `npm run build` produces:**

| App | Output directory | Contents |
|-----|-----------------|----------|
| Frontend | `main-file/listocean/core/public/build/` | Bootstrap 5 + SCSS + jQuery bundles |
| Admin | `sellupnow-admin/public/admin-build/` | Vue 3 + Tailwind + all dashboard JS |

These compiled assets are served directly by Nginx as static files. **Rebuild after every code change that touches `resources/`.**

---

## 8. Run the Web Installer (Database Setup)

Both apps include a browser-based installation wizard that creates all database tables and seeds default data.

> **Purchase code / license verification is disabled.** The original Xgenious installer downloaded a SQL dump from the license server containing Codecanyon demo data and overwrote the database with it. That behaviour has been removed. Both installers now run `php artisan migrate:fresh --seed` which creates a **clean database from your own migrations**. Never enter a purchase code — just click Skip or Next when prompted.

> **Database WARNING:** `migrate:fresh` drops ALL existing tables before recreating them. Only run the installer on a **fresh, empty database**. Never run it on a database that already has live user data.

---

### 8.1 Run the Admin Panel Installer FIRST

Open in your browser:
```
https://yourdomain.com/admin/install
```

| Step | Screen | Action |
|------|--------|--------|
| 1 | Welcome | Click **Next** |
| 2 | Server Requirements | All rows must show ✅. If something is red, install the missing PHP extension then refresh. |
| 3 | Folder Permissions | All rows must show ✅. If not, run: `sudo chmod -R 775 storage bootstrap/cache` |
| 4 | Database Setup | Host: `127.0.0.1` · Database: `sellupnow_admin` · User: `sellupnow` · Password: your password |
| 5 | Application Setup | App Name: `SellUpNow Admin` · Admin Email: your email · Admin Password: strong password · App URL: `https://yourdomain.com` |
| 6 | Purchase Verification | **Skip** — do not enter a code |
| 7 | Installation | Wizard runs `migrate:fresh --seed` — wait for the green success banner |
| 8 | Finish | "Installation Completed Successfully" |

**The admin installer seeds:**
- Admin roles: Super Admin, Admin, Editor, Manager
- Full RBAC permission set
- Default currencies, payment gateways, social auth providers
- Default theme colours, homepage layout

---

### 8.2 Run the Frontend Installer

Open in your browser:
```
https://yourdomain.com/install
```

Use the same wizard, entering:

| Field | Value |
|-------|-------|
| Database | `listocean_db` |
| User | `sellupnow` |
| Password | your DB password |
| App URL | `https://yourdomain.com` |

**The frontend installer seeds:**
- 4 user roles with permissions
- English (UK) as the default language
- Homepage page record, `static_options.home_page` pointer, and 6 starter PageBuilder sections (Header, Browse Categories, Listings, Recent Listings, Top Listings, Marketplace) — the homepage will render sections immediately after install

---

### 8.3 Disable Both Installers Immediately After

```bash
# Frontend
sed -i 's/INSTALLER_ENABLED=true/INSTALLER_ENABLED=false/' \
  /var/www/sellupnow/main-file/listocean/core/.env

# Admin panel
sed -i 's/INSTALLER_ENABLED=true/INSTALLER_ENABLED=false/' \
  /var/www/sellupnow/sellupnow-admin/.env

# Verify both are now false
grep INSTALLER_ENABLED /var/www/sellupnow/main-file/listocean/core/.env
grep INSTALLER_ENABLED /var/www/sellupnow/sellupnow-admin/.env
```

After this, visiting `https://yourdomain.com/install` must return 404. If it still loads, run `php artisan config:clear` then recheck.

---

## 9. Post-Install Artisan Commands

Run these after both installers complete. These commands are also safe to re-run on every future deployment.

```bash
# ── Frontend ──────────────────────────────────────────────────────────────
cd /var/www/sellupnow/main-file/listocean/core

# Apply any additional migrations beyond the installer
php artisan migrate --force

# Create the public storage symlink (makes /storage/ URL work for uploads)
php artisan storage:link

# Create queue and session tables (required when QUEUE_CONNECTION=database)
php artisan queue:table 2>/dev/null; true
php artisan session:table 2>/dev/null; true
php artisan migrate --force

# Optimise for production (run ONLY after INSTALLER_ENABLED=false)
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache


# ── Admin Panel ───────────────────────────────────────────────────────────
cd /var/www/sellupnow/sellupnow-admin

php artisan migrate --force
php artisan storage:link

php artisan queue:table 2>/dev/null; true
php artisan session:table 2>/dev/null; true
php artisan migrate --force

# Import Ghana location data (regions → cities for listing address dropdowns)
# NOTE: This command belongs to the admin app — run it from sellupnow-admin, not the frontend.
php artisan listocean:import-locations --country="Ghana"

php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 10. Nginx Configuration

Both apps share **one domain and one Nginx server block**. No subdomain is needed for the admin panel.

**Routing logic:**

| URL Path | Handled by | Public directory |
|----------|-----------|-----------------|
| `/admin-build/*` | Nginx static | `sellupnow-admin/public/` |
| `/admin/storage/*` | Nginx static | `sellupnow-admin/public/storage/` |
| `/admin/*` | PHP-FPM → admin `index.php` | `sellupnow-admin/public/` |
| `/build/*` | Nginx static | `listocean/core/public/` |
| `/storage/*` | Nginx static | `listocean/core/public/storage/` |
| `/*` (everything else) | PHP-FPM → frontend `index.php` | `listocean/core/public/` |

```bash
sudo nano /etc/nginx/sites-available/sellupnow
```

Paste this configuration (replace `yourdomain.com` with your actual domain):

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;

    client_max_body_size 50M;
    charset utf-8;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";
    add_header Referrer-Policy "strict-origin-when-cross-origin";

    # Block all dotfiles (.env, .git, .htaccess, etc.)
    location ~ /\.(?!well-known).* {
        deny all;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    # ── 1. Admin Vite compiled assets ─────────────────────────────────────
    # Declared BEFORE the /admin PHP block.
    # The admin Vite build outputs to public/admin-build/ — not public/build/ —
    # so there is zero URL collision with the frontend's /build/ path.
    location ^~ /admin-build/ {
        root /var/www/sellupnow/sellupnow-admin/public;
        expires max;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    # ── 2. Admin file uploads (logos, badges, documents) ──────────────────
    location ^~ /admin/storage/ {
        alias /var/www/sellupnow/sellupnow-admin/public/storage/;
        expires 30d;
        access_log off;
    }

    # ── 3. Admin panel — all /admin/* PHP requests ─────────────────────────
    location ^~ /admin {
        root /var/www/sellupnow/sellupnow-admin/public;
        index index.php;
        try_files $uri $uri/ /index.php?$query_string;

        location ~ \.php$ {
            fastcgi_pass   unix:/var/run/php/php8.2-fpm.sock;
            fastcgi_index  index.php;
            fastcgi_param  SCRIPT_FILENAME $document_root$fastcgi_script_name;
            include        fastcgi_params;
        }
    }

    # ── 4. Frontend Vite compiled assets ──────────────────────────────────
    location ^~ /build/ {
        root /var/www/sellupnow/main-file/listocean/core/public;
        expires max;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    # ── 5. Frontend file uploads (listing images, profile photos) ─────────
    location ^~ /storage/ {
        root /var/www/sellupnow/main-file/listocean/core/public;
        expires 30d;
        access_log off;
    }

    # ── 6. Frontend — everything else ─────────────────────────────────────
    location / {
        root /var/www/sellupnow/main-file/listocean/core/public;
        index index.php;
        try_files $uri $uri/ /index.php?$query_string;
        error_page 404 /index.php;

        location ~ \.php$ {
            fastcgi_pass   unix:/var/run/php/php8.2-fpm.sock;
            fastcgi_index  index.php;
            fastcgi_param  SCRIPT_FILENAME $document_root$fastcgi_script_name;
            include        fastcgi_params;
        }
    }
}
```

### Enable the site

```bash
# Remove the default Nginx placeholder site
sudo rm -f /etc/nginx/sites-enabled/default

# Enable SellUpNow
sudo ln -s /etc/nginx/sites-available/sellupnow /etc/nginx/sites-enabled/

# Test syntax before reloading
sudo nginx -t

# Reload (zero downtime)
sudo systemctl reload nginx
```

---

## 11. HTTPS with Let's Encrypt

One domain — one SSL certificate. Certbot modifies the Nginx config automatically.

```bash
# Obtain certificate and auto-configure Nginx
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
# When prompted, choose option 2 (Redirect HTTP to HTTPS)

# Test automatic renewal
sudo certbot renew --dry-run

# Verify the renewal timer is active
sudo systemctl status certbot.timer
```

After Certbot completes, update both `.env` files to use `https://`:

```bash
# Frontend
sed -i 's|APP_URL=http://yourdomain.com|APP_URL=https://yourdomain.com|g' \
  /var/www/sellupnow/main-file/listocean/core/.env

# Admin panel (update all four URL variables)
sed -i \
  -e 's|APP_URL=http://|APP_URL=https://|g' \
  -e 's|CUSTOMER_WEB_URL=http://|CUSTOMER_WEB_URL=https://|g' \
  -e 's|LISTOCEAN_APP_URL=http://|LISTOCEAN_APP_URL=https://|g' \
  -e 's|LISTOCEAN_API_BASE=http://|LISTOCEAN_API_BASE=https://|g' \
  /var/www/sellupnow/sellupnow-admin/.env

# Re-cache config so the updated URLs are used
cd /var/www/sellupnow/main-file/listocean/core && php artisan config:cache
cd /var/www/sellupnow/sellupnow-admin && php artisan config:cache
```

---

## 12. Queue Workers (Supervisor)

The queue handles: email delivery, wallet transactions, membership activation, escrow timers, push notification dispatch, and Firebase messaging.

### Frontend worker config

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

### Admin panel worker config

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

### Start workers

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start all
sudo supervisorctl status

# Expected output:
# sellupnow-admin-worker:sellupnow-admin-worker_00          RUNNING
# sellupnow-frontend-worker:sellupnow-frontend-worker_00    RUNNING
# sellupnow-frontend-worker:sellupnow-frontend-worker_01    RUNNING
```

---

## 13. Scheduler (Cron)

The Laravel scheduler handles: membership expiry, featured ad expiry, boost expiry, escrow auto-release, and sitemap generation.

```bash
sudo crontab -e -u www-data
```

Add these two lines:

```cron
* * * * * cd /var/www/sellupnow/main-file/listocean/core && php artisan schedule:run >> /dev/null 2>&1
* * * * * cd /var/www/sellupnow/sellupnow-admin && php artisan schedule:run >> /dev/null 2>&1
```

Verify they were saved:

```bash
sudo crontab -l -u www-data
```

**Scheduled jobs:**

| Job | Schedule | What it does |
|-----|----------|-------------|
| `ExpireMemberships` | Daily 00:05 | Marks expired user memberships |
| `DowngradeExpiredUsers` | Daily 00:10 | Deactivates listings over free-plan limit |
| `ExpireFeaturedAds` | Hourly | Deactivates expired featured activations |
| `ExpireBoosts` | Hourly | Marks expired listing boosts |
| `EscrowAutoRelease` | Hourly | Releases escrowed funds after buyer deadline |
| `GenerateSitemap` | Weekly (Sun 02:00) | Rebuilds `public/sitemap.xml` |

---

## 14. File Permissions

> ⚠️ **Run this section immediately after uploading the codebase (Section 4) and before running the web installer (Section 8).** The installer writes to `storage/` and `bootstrap/cache/` — if those directories are not writable by `www-data`, the installer will fail with a cryptic error.

```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/sellupnow

# Files readable, directories traversable
sudo find /var/www/sellupnow -type f -exec chmod 644 {} \;
sudo find /var/www/sellupnow -type d -exec chmod 755 {} \;

# Storage and bootstrap/cache must be writable by www-data
sudo chmod -R 775 /var/www/sellupnow/main-file/listocean/core/storage
sudo chmod -R 775 /var/www/sellupnow/main-file/listocean/core/bootstrap/cache
sudo chmod -R 775 /var/www/sellupnow/sellupnow-admin/storage
sudo chmod -R 775 /var/www/sellupnow/sellupnow-admin/bootstrap/cache

# Verify storage symlinks
ls -la /var/www/sellupnow/main-file/listocean/core/public/storage
ls -la /var/www/sellupnow/sellupnow-admin/public/storage
# Both should show: storage -> ../storage/app/public
```

---

## 15. Admin Panel First-Login Setup

Navigate to `https://yourdomain.com/admin` and log in with the credentials you entered during the web installer.

Complete these steps **in order** before going live:

---

### Step 1 — Configure SMTP Mail

`Admin → Settings → Mail Configuration`

| Field | Value |
|-------|-------|
| Mail Driver | SMTP |
| Mail Host | e.g. `smtp.gmail.com` or your SendGrid host |
| Mail Port | 587 (TLS) or 465 (SSL) |
| Mail Username | your SMTP account username |
| Mail Password | your SMTP password |
| Encryption | TLS |
| Mail From Address | `noreply@yourdomain.com` |
| Mail From Name | `SellUpNow` |

Click **Send Test Email** after saving to confirm delivery works.

---

### Step 2 — Configure Payment Gateway

`Admin → Settings → Payment Gateways`

- Enable **Paystack**
- Enter your Paystack **Live** Secret Key and Public Key (from your Paystack dashboard)
- Set currency: **GHS**

> ⚠️ Use **Live** keys, not Test keys. Test keys will silently fail for real transactions.

---

### Step 3 — Set Site Identity

`Admin → Settings → General Settings`

- Upload logo and favicon
- Set App Name, support email, support phone number
- Set default country and currency

---

### Step 4 — Create Membership Plans

`Admin → Membership Plans → Create`

Create them **in this exact order** so their database IDs match what the mobile app expects:

| # | Plan Name | Price | Listing Limit | Duration |
|---|-----------|-------|---------------|----------|
| 1 | Free | ₵0 | 5 | Lifetime |
| 2 | Starter | ₵49 | 25 | 30 days |
| 3 | Pro | ₵149 | 100 | 30 days |
| 4 | Business | ₵399 | Unlimited | 30 days |

> Full feature configuration is in `MEMBERSHIP-SYSTEM.md` §6.

---

### Step 5 — Create Featured Ad Packages

`Admin → Featured Ad Packages → Create`

| Package | Price | Duration |
|---------|-------|----------|
| Bronze Feature | ₵40 | 7 days |
| Silver Feature | ₵70 | 14 days |
| Gold Feature | ₵130 | 30 days |

---

### Step 6 — Import Location Data

```bash
cd /var/www/sellupnow/sellupnow-admin
php artisan listocean:import-locations --country="Ghana"
```

Populates countries → states → cities dropdowns on the listing form.

---

### Step 7 — Configure Firebase Push Notifications

`Admin → Settings → Firebase`

- Paste your Firebase Server Key
  (Firebase Console → Project Settings → Cloud Messaging → Server key)

---

### Step 8 — Set Escrow Settings

`Admin → Escrow → Settings`

| Setting | Recommended |
|---------|------------|
| Commission percentage | 5% |
| Auto-release after | 7 days |
| Seller accept deadline | 3 days |

---

### Step 9 — Create Listing Categories

`Admin → Listing Categories → Create`

Add your marketplace categories (e.g. Vehicles, Property, Electronics, Fashion, Jobs, Services) and their subcategories. At least one category must exist before users can post listings.

---

## 16. Smoke Tests

### Server-side

```bash
# Both apps should return HTTP 200
curl -o /dev/null -sw "Frontend: %{http_code}\n" https://yourdomain.com
curl -o /dev/null -sw "Admin:    %{http_code}\n" https://yourdomain.com/admin

# Queue workers running
sudo supervisorctl status

# Cron registered
sudo crontab -l -u www-data

# Scheduler job list
cd /var/www/sellupnow/main-file/listocean/core && php artisan schedule:list

# Storage symlinks
ls -la public/storage
ls -la /var/www/sellupnow/sellupnow-admin/public/storage

# Recent log lines (should be empty or INFO level only — no errors)
tail -20 /var/www/sellupnow/main-file/listocean/core/storage/logs/laravel.log
tail -20 /var/www/sellupnow/sellupnow-admin/storage/logs/laravel.log
```

### Browser checklist

| Test | Expected result |
|------|----------------|
| `https://yourdomain.com` | Homepage loads, categories visible |
| `https://yourdomain.com/login` | Login form renders |
| `https://yourdomain.com/register` | Registration form renders |
| Register a new user account | Redirects to dashboard, email sent |
| Post a new listing | Listing saved with status = Pending |
| `https://yourdomain.com/admin` | Admin login page loads |
| Login to admin with installer credentials | Admin dashboard loads |
| Admin → Listing Moderation | Shows the pending listing |
| Approve the listing | Status changes to Active |
| Frontend — listing is now visible | Listing appears in search results |
| `https://yourdomain.com/user/wallet` | Wallet shows ₵0.00 |
| `https://yourdomain.com/user/membership` | 4 plan cards displayed |
| Admin → Mail Config → Send test email | Email arrives in inbox |

---

## 17. Security Checklist

### Must be done before going live

- [ ] `APP_ENV=production` in both `.env` files
- [ ] `APP_DEBUG=false` in both `.env` files
- [ ] `INSTALLER_ENABLED=false` in both `.env` files — visiting `/install` returns 404
- [ ] `QUEUE_CONNECTION=database` (not `sync`) in both `.env` files
- [ ] HTTPS enforced — HTTP permanently redirects to HTTPS (Certbot sets this)
- [ ] MySQL `sellupnow` user has no superuser privileges — not using `root` credentials
- [ ] Strong, unique `APP_KEY` in both apps (`php artisan key:generate`)
- [ ] Strong MySQL password
- [ ] Nginx `deny all` for dotfiles is active (config above protects `.env` from HTTP access)
- [ ] `/update` routes return 404 (already blocked by middleware)
- [ ] Supervisor workers run as `www-data`, not `root`
- [ ] `storage/` and `bootstrap/cache/` writable by `www-data` only
- [ ] Firewall: only ports 22 (SSH), 80 (HTTP), 443 (HTTPS) open

```bash
# Set up UFW firewall
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
sudo ufw status
```

### Recommended

- [ ] Change SSH default port from 22 to a non-standard port
- [ ] Disable root SSH login (`PermitRootLogin no` in `/etc/ssh/sshd_config`)
- [ ] Install Fail2Ban to block SSH brute-force
- [ ] Automated daily MySQL backups to off-server storage (S3, Backblaze, etc.)
- [ ] Uptime monitoring (UptimeRobot, BetterUptime) pointed at `https://yourdomain.com`
- [ ] Switch to Redis for cache, session, and queue (`CACHE_DRIVER=redis`, `SESSION_DRIVER=redis`, `QUEUE_CONNECTION=redis`)
- [ ] Log rotation (Laravel already uses `LOG_CHANNEL=daily`)

---

## 18. Deploying Code Updates

Run this on the VPS every time you push new code to GitHub:

```bash
cd /var/www/sellupnow

# 1. Pull latest code
git pull origin main

# 2. Fix ownership on any newly added files
sudo chown -R www-data:www-data .

# ── Frontend ──────────────────────────────────────────────────────────────
cd /var/www/sellupnow/main-file/listocean/core

# 3. Install any new Composer packages
composer install --no-dev --optimize-autoloader

# 4. Run new migrations
php artisan migrate --force

# 5. Rebuild assets (skip if no changes in resources/)
npm install && npm run build

# 6. Clear and re-cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ── Admin Panel ───────────────────────────────────────────────────────────
cd /var/www/sellupnow/sellupnow-admin

composer install --no-dev --optimize-autoloader
php artisan migrate --force
npm install && npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ── Restart workers to pick up new code ──────────────────────────────────
sudo supervisorctl restart all
sudo supervisorctl status
```

> **Tip:** If only PHP logic changed (no JS/CSS), skip `npm run build`. If only `.env` changed, just run `php artisan config:cache` and `sudo supervisorctl restart all`.

---

## 19. Quick Reference Commands

```bash
# ── Caching ───────────────────────────────────────────────────────────────

# Re-cache after .env or config change
php artisan config:cache && php artisan route:cache && php artisan view:cache

# Clear ALL caches (use when troubleshooting)
php artisan config:clear && php artisan cache:clear && php artisan view:clear && php artisan route:clear

# ── Database ──────────────────────────────────────────────────────────────

# Run pending migrations
php artisan migrate --force

# Check migration status
php artisan migrate:status

# ── Queue workers ─────────────────────────────────────────────────────────

# Restart after code deploy
sudo supervisorctl restart all

# Check worker status
sudo supervisorctl status

# View worker logs live
tail -f /var/www/sellupnow/main-file/listocean/core/storage/logs/worker.log
tail -f /var/www/sellupnow/sellupnow-admin/storage/logs/worker.log

# ── Application logs ──────────────────────────────────────────────────────

tail -f /var/www/sellupnow/main-file/listocean/core/storage/logs/laravel.log
tail -f /var/www/sellupnow/sellupnow-admin/storage/logs/laravel.log

# ── Asset builds ──────────────────────────────────────────────────────────

cd /var/www/sellupnow/main-file/listocean/core && npm run build
cd /var/www/sellupnow/sellupnow-admin && npm run build

# ── Services ──────────────────────────────────────────────────────────────

sudo systemctl status php8.2-fpm
sudo systemctl status mysql
sudo nginx -t && sudo systemctl reload nginx

# ── Scheduler ─────────────────────────────────────────────────────────────

# List all scheduled jobs and next run times
cd /var/www/sellupnow/main-file/listocean/core && php artisan schedule:list

# Manually run the scheduler (for testing)
php artisan schedule:run
```

---

## 20. Troubleshooting

### 20.1 `Failed to load resource: 500 Internal Server Error`

A 500 response in the browser console means Laravel threw an unhandled exception before producing a response. Work through the steps below in order.

#### Step 1 — Read the real error from the log

```bash
# Frontend app
tail -n 50 /var/www/sellupnow/main-file/listocean/core/storage/logs/laravel.log

# Admin panel
tail -n 50 /var/www/sellupnow/sellupnow-admin/storage/logs/laravel.log

# Nginx — catches PHP-FPM fatals that Laravel never sees
sudo tail -n 50 /var/log/nginx/error.log
```

The `local.ERROR` or `CRITICAL` line will name the exact file and line. **Fix that specific error first** before continuing with the steps below.

#### Step 2 — Clear all caches

Stale compiled views or config cache from a previous `git pull` frequently causes 500s.

```bash
# Frontend
cd /var/www/sellupnow/main-file/listocean/core
php artisan config:clear && php artisan cache:clear && php artisan view:clear && php artisan route:clear

# Admin
cd /var/www/sellupnow/sellupnow-admin
php artisan config:clear && php artisan cache:clear && php artisan view:clear && php artisan route:clear
```

Then re-cache:

```bash
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

#### Step 3 — Verify storage & upload directory permissions

PHP-FPM runs as `www-data`. If it cannot write to `storage/` or `public/assets/uploads/`, Laravel throws a 500 on any page that attempts file I/O.

```bash
sudo chown -R www-data:www-data \
  /var/www/sellupnow/main-file/listocean/core/storage \
  /var/www/sellupnow/main-file/listocean/core/public/assets/uploads \
  /var/www/sellupnow/sellupnow-admin/storage

sudo chmod -R 775 \
  /var/www/sellupnow/main-file/listocean/core/storage \
  /var/www/sellupnow/main-file/listocean/core/public/assets/uploads \
  /var/www/sellupnow/sellupnow-admin/storage
```

Also check for **broken symlinks** masquerading as directories (this was a known failure point on this project):

```bash
find /var/www/sellupnow/main-file/listocean/core/public/assets/uploads -maxdepth 2 -type l | while read l; do
  [ -e "$l" ] || echo "BROKEN SYMLINK: $l"
done
```

If a broken symlink is found, replace it with a real directory:

```bash
rm /path/to/broken-symlink
mkdir -p /path/to/broken-symlink
sudo chown www-data:www-data /path/to/broken-symlink
sudo chmod 775 /path/to/broken-symlink
```

#### Step 4 — Confirm `.env` is present and `APP_KEY` is set

```bash
grep APP_KEY /var/www/sellupnow/main-file/listocean/core/.env
grep APP_KEY /var/www/sellupnow/sellupnow-admin/.env
```

If either is blank or the file is missing, generate a key:

```bash
cd /var/www/sellupnow/main-file/listocean/core && php artisan key:generate --force
cd /var/www/sellupnow/sellupnow-admin              && php artisan key:generate --force
```

#### Step 5 — Confirm Vite asset manifest exists

A missing `public/build/manifest.json` causes 500s on every page because Laravel's `@vite()` directive throws when it cannot find the manifest.

```bash
ls -la /var/www/sellupnow/main-file/listocean/core/public/build/manifest.json
ls -la /var/www/sellupnow/sellupnow-admin/public/build/manifest.json
```

If either is absent, rebuild:

```bash
cd /var/www/sellupnow/main-file/listocean/core && npm ci && npm run build
cd /var/www/sellupnow/sellupnow-admin           && npm ci && npm run build
```

#### Step 6 — Restart PHP-FPM and queue workers

```bash
sudo systemctl restart php8.2-fpm
sudo supervisorctl restart all
```

#### Step 7 — Ensure `APP_DEBUG=false` in production

`APP_DEBUG=true` does not cause 500s, but it exposes database credentials, file paths, and stack traces to the browser. Always keep it off in production `.env` files:

```env
APP_DEBUG=false
APP_ENV=production
```

---

### 20.2 `GET /user/listing/add` → 500 Internal Server Error

This route belongs to the **frontend app** (ListOcean). It loads the "Post a Listing" form for authenticated users. The 500 almost always comes from one of the issues below.

#### Step 1 — Check the log first

```bash
tail -n 80 /var/www/sellupnow/main-file/listocean/core/storage/logs/laravel.log | grep -A 5 "ERROR\|Exception"
```

The stack trace will identify the exact cause. Common messages and their fixes are listed below.

#### Step 2 — Upload directory missing or not writable

The listing-add controller creates thumbnails and saves images under `public/assets/uploads/media-uploader/`. If that directory does not exist or is not writable by `www-data`, the page 500s immediately on load.

```bash
# Confirm the directory exists and is a real directory (not a broken symlink)
ls -la /var/www/sellupnow/main-file/listocean/core/public/assets/uploads/media-uploader

# If it is a broken symlink, remove it and create a real directory
rm /var/www/sellupnow/main-file/listocean/core/public/assets/uploads/media-uploader
mkdir -p /var/www/sellupnow/main-file/listocean/core/public/assets/uploads/media-uploader/tiny
sudo chown -R www-data:www-data /var/www/sellupnow/main-file/listocean/core/public/assets/uploads
sudo chmod -R 775 /var/www/sellupnow/main-file/listocean/core/public/assets/uploads
```

#### Step 3 — No listing packages / membership plans configured

The listing-add page queries the `packages` or `listing_packages` table to populate the plan selector. If the table is empty the controller may throw rather than returning an empty collection.

```bash
mysql -u root -p listocean_db -e "SELECT COUNT(*) FROM packages;"
```

If zero rows, go to **Admin → Packages** and create at least one free plan, then refresh.

#### Step 4 — No categories in the database

The form requires at least one category to populate the category dropdown. If the table is empty the controller can throw.

```bash
mysql -u root -p listocean_db -e "SELECT id, name FROM categories LIMIT 10;"
```

If no rows, seed categories via **Admin → Categories → Add Category**, then refresh.

#### Step 5 — Missing or misconfigured `listocean_core_path()`

`sellupnow-admin/app/helpers.php` contains the `listocean_core_path()` helper used by upload controllers to resolve the correct absolute path to the frontend's `core/` directory. If that helper resolves to a wrong path, `mkdir()` will fail silently and the controller will 500.

Verify the helper returns the correct path:

```bash
cd /var/www/sellupnow/sellupnow-admin
php artisan tinker --execute="echo listocean_core_path('public/assets/uploads/media-uploader');"
```

Expected output: `/var/www/sellupnow/main-file/listocean/core/public/assets/uploads/media-uploader`

If the path is wrong, check the `listocean_core_path()` function in `app/helpers.php` and ensure `realpath(base_path('..'))` resolves to `/var/www/sellupnow/sellupnow-admin/..` = `/var/www/sellupnow`, and that slashes are all forward slashes.

#### Step 6 — User not in a valid membership / not logged in

`/user/listing/add` requires an authenticated user. If session cookies are not being set (misconfigured `SESSION_DOMAIN`, wrong `APP_URL`, or HTTP vs HTTPS mismatch), the middleware redirects or throws.

```bash
grep -E "APP_URL|SESSION_DOMAIN|SESSION_DRIVER" /var/www/sellupnow/main-file/listocean/core/.env
```

Ensure `APP_URL` matches the actual domain being accessed (including `https://` in production).

#### Step 7 — Clear caches and retry

After any of the above fixes:

```bash
cd /var/www/sellupnow/main-file/listocean/core
php artisan config:clear && php artisan cache:clear && php artisan view:clear
php artisan config:cache && php artisan route:cache && php artisan view:cache
sudo systemctl restart php8.2-fpm
```
