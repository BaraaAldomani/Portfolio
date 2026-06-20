# Deploy to Hostinger — Baraa Portfolio

This is a **Laravel** site. The good news: Hostinger runs PHP, so it runs Laravel.
**Node.js is only a build tool** and is already done for you — the compiled CSS/JS
(`public/build/`) and PHP dependencies (`vendor/`) are inside the zip, so you do
**not** need Node, npm, or Composer on the server.

The only real adaptations for Hostinger are: the database is **MySQL** (not the
Postgres used in development), and the web root must point at the **`public/`**
folder. Both are handled below.

---

## What's in the zip

- The full Laravel app, production-ready (`vendor/` + `public/build/` included).
- `.env` — production config with an `APP_KEY` already generated. You only edit
  the **database** lines and your **domain**.
- `.htaccess` (at the root) — a *fallback* that routes into `/public` if you
  cannot change the document root.

## Requirements on Hostinger

- **PHP 8.3 or newer** (hPanel → Advanced → PHP Configuration → select 8.3/8.4).
  Laravel 13 requires PHP 8.3+.
- A **MySQL** database.
- **SSH / Terminal** access to run a few one-time commands (Hostinger Premium and
  Business include SSH; there's also a browser Terminal in hPanel). A no-SSH path
  is described at the end.

---

## Step 1 — Create the database
hPanel → **Databases → MySQL Databases**. Create a database **and** a user, and
note the **database name, username, password**. (Hostinger prefixes them, e.g.
`u123456_baraa`.)

## Step 2 — Upload + extract
hPanel → **File Manager** → open **`public_html`** → upload
`baraa-portfolio-hostinger.zip` → right-click → **Extract** here.
(Or upload via FTP.) After extracting you should see `artisan`, `app`, `public`,
`vendor`, `.env`, etc. inside `public_html`.

## Step 3 — Edit `.env`
Open `.env` in File Manager and set:
- `APP_URL=https://yourdomain.com`
- `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` → the values from Step 1.

Leave `APP_KEY` as it is (already set). Save.

## Step 4 — Point the domain at `public/`  (important)
**Preferred:** hPanel → **Domains → your domain → Document Root** → set it to
`public_html/public`. This is the cleanest and most secure option.

**If your plan won't let you change it:** do nothing — the included root
`.htaccess` already forwards requests into `/public`. (Changing the document root
is still preferable when possible.)

## Step 5 — Run the one-time setup commands
Open **SSH** or hPanel → **Advanced → Terminal**, `cd` into the project folder
(the one containing `artisan`), and run:

```bash
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

- `migrate` creates all tables (projects, contact_messages, sessions, cache …).
- `db:seed` loads your 4 projects.
- the three `*:cache` commands make the site faster.

> Whenever you later change `.env`, run `php artisan optimize:clear` and then
> `php artisan config:cache` again.

## Step 6 — Permissions
`storage/` and `bootstrap/cache/` must be writable. Via SSH:
```bash
chmod -R 775 storage bootstrap/cache
```

## Step 7 — Test
Open `https://yourdomain.com` → it redirects to `/ar`. Check `/en`, `/ar/about`,
`/en/projects`, and submit the **contact form**. Submissions are saved to the
`contact_messages` table — read them in hPanel → **phpMyAdmin**.

---

## No SSH / Terminal on your plan?
Everything except the database setup is already prepared in the zip. To run the
migrations + seed without SSH, use **hPanel → Advanced → Cron Jobs**: add a job
whose command is (adjust the path Hostinger shows for your account):

```
php /home/uXXXXXXXX/domains/yourdomain.com/public_html/artisan migrate --seed --force
```

Set it to run once (e.g. every minute), let it run a single time, then delete the
cron job. After that the site is live.

---

## Everyday tasks
- **Read contact messages:** hPanel → phpMyAdmin → `contact_messages` table.
- **Change a project image:** in phpMyAdmin set that project's `image_path`
  (e.g. `images/projects/tawazoun.jpg`) and upload the file to
  `public/images/projects/`.
- **Replace a photo (portrait/logo):** overwrite the file in `public/images/`
  with the same name, then hard-refresh the browser (Ctrl+Shift+R) to bypass
  the cache.
