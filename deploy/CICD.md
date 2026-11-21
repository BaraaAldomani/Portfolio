# Automated deploy: GitHub Actions → Hostinger (SSH)

Push to `main`/`master` and the site deploys itself. GitHub builds `vendor/` and
`public/build/` on its runners, rsyncs the app to Hostinger over SSH, then runs
`migrate` + the cache commands on the server. Your production `.env` and any
images you uploaded on the server are **never overwritten** (rsync excludes
`.env` and never deletes remote files).

The pipeline lives in [`.github/workflows/deploy.yml`](../.github/workflows/deploy.yml).

---

## One-time setup

### 1. Generate a deploy SSH key (on your machine)

```powershell
ssh-keygen -t ed25519 -C "github-deploy" -f "$HOME\.ssh\hostinger_deploy"
```

Press **Enter twice** for an empty passphrase (automation can't type one). This
makes two files in `C:\Users\<you>\.ssh\`: `hostinger_deploy` (private) and
`hostinger_deploy.pub` (public). Do **not** commit them.

### 2. Add the public key to Hostinger

hPanel → **Advanced → SSH Access**. Make sure SSH is **enabled** and note the
**IP/host** and **port** (Hostinger shared hosting is usually **65002**, not 22).
Use **Manage SSH keys → Import** and paste the contents of
`hostinger_deploy.pub`.

Test it from your machine:

```powershell
ssh -p 65002 -i "$HOME\.ssh\hostinger_deploy" u744145577@147.93.73.123
```

You should land in your home dir with no password prompt.

### 3. Confirm your deploy path

Over that SSH session, the folder that contains `artisan`, `app`, `public`,
`vendor` … is your `DEPLOY_PATH`. This account hosts multiple sites under
`~/domains/`, so the portfolio path is:

```
/home/u744145577/domains/baraa.rakeez-llc.com/public_html
```

> The server `.env` is the source of truth and is excluded from rsync. Edit it
> directly on the server when secrets change.

### 4. Make sure the app exists on the server once

The pipeline **updates** an existing install; it does not bootstrap a brand-new
one. The first install is already done (the app is in `public_html`). If you ever
start fresh, do the first deploy the manual way — see [`DEPLOY.md`](DEPLOY.md).

### 5. Add the repo secrets in GitHub

GitHub repo → **Settings → Secrets and variables → Actions → New repository
secret**. Add each:

| Secret name       | Value (example)                                       | Notes |
|-------------------|-------------------------------------------------------|-------|
| `SSH_PRIVATE_KEY` | *(paste the whole `hostinger_deploy` file)*           | Include the `-----BEGIN/END-----` lines. |
| `SSH_HOST`        | `147.93.73.123`                                       | From hPanel → SSH Access. |
| `SSH_USER`        | `u744145577`                                          | |
| `SSH_PORT`        | `65002`                                               | Check hPanel; usually 65002. |
| `DEPLOY_PATH`     | `/home/u744145577/domains/baraa.rakeez-llc.com/public_html` | The folder containing `artisan`. |
| `PHP_BIN`         | `/opt/alt/php83/usr/bin/php`                           | Server CLI `php` is 8.2; the app needs 8.3. This CloudLinux path is PHP 8.3.31. |
| `TELEGRAM_BOT_TOKEN` | `123456:ABC...`                                    | Optional. Contact-form alerts; from @BotFather. |
| `TELEGRAM_CHAT_ID`   | `123456789`                                        | Optional. Your chat id with the bot. |
| `LEAD_NOTIFY_EMAIL`  | `you@example.com`                                  | Optional. Email alerts (needs `MAIL_*` set on the server). |

The three optional ones are written into the server `.env` on every deploy by
`scripts/set-env.php` (empty secrets are skipped). Check them with
`php artisan leads:test` on the server.

GitHub secrets are write-only — you can't read them back, only overwrite. That's
expected.

---

## Deploy

```powershell
git push origin master
```

Watch it in GitHub → the **Actions** tab. The `Deploy to Hostinger` workflow runs
build → deploy. You can also trigger it by hand from that tab (**Run workflow**,
thanks to `workflow_dispatch`).

### Want a manual confirmation gate instead of auto-deploy?

Use GitHub **Environments**: Settings → Environments → New environment
`production` → enable **Required reviewers** (add yourself). Then add
`environment: production` to the job in
[`deploy.yml`](../.github/workflows/deploy.yml). Each deploy will then wait for
your approval click.

---

## Admin dashboard (Filament) — first deploy only

Every deploy already runs `migrate`, `filament:assets`, `storage:link` and the
cache rebuilds. Two extra steps are needed **once**, on the first deploy after
the dashboard was added. They are deliberately *not* in the pipeline: re-running
the seeder on every deploy would overwrite content you edit in the dashboard.

Over SSH, from `$DEPLOY_PATH` (use the 8.3 binary, e.g. `/opt/alt/php83/usr/bin/php`):

```bash
# 1. Load the initial content + settings (services, work history, metrics,
#    SEO text, theme colours…). Run this ONCE.
/opt/alt/php83/usr/bin/php artisan db:seed --force

# 2. Create your admin login. Reads ADMIN_EMAIL / ADMIN_PASSWORD from .env,
#    or pass them inline. Safe to re-run (updates the same user).
/opt/alt/php83/usr/bin/php artisan app:create-admin --email=you@example.com --password='a-strong-password'
```

Then sign in at `https://your-domain/admin`.

Add these to the server `.env` if you prefer the env-driven form of step 2:

```dotenv
ADMIN_EMAIL=you@example.com
ADMIN_PASSWORD=a-strong-password
ADMIN_NAME=Baraa
```

Notes:
- Uploaded images live in `storage/app/public/images/...` and are served through
  the `public/storage` symlink. rsync runs without `--delete`, so uploads survive
  deploys.
- Only users with `is_admin = true` can open `/admin`; `app:create-admin` sets it.
- Migrations were verified against MySQL 8 (the `settings` table uses the
  reserved words `group`/`key`; Laravel quotes them, so it is fine).

---

## Troubleshooting

- **`Permission denied (publickey)`** — public key isn't imported on Hostinger,
  or `SSH_PORT`/`SSH_USER`/`SSH_HOST` is wrong. Re-test the raw
  `ssh -p … -i …` command from step 2.
- **`Host key verification failed`** — the runner couldn't `ssh-keyscan` the
  host; check `SSH_HOST`/`SSH_PORT`.
- **`php: command not found` / `Composer dependencies require PHP >= 8.3` (the
  CLI default is 8.2)** — set the `PHP_BIN` secret to the full 8.3 path
  `/opt/alt/php83/usr/bin/php`. Find it with:
  `for p in /opt/alt/php8*/usr/bin/php; do $p -v | head -1; done`
- **`rsync: command not found` on the server** — rare on Hostinger. If it
  happens, switch the transfer to SFTP (ask and I'll adjust the workflow).
- **500 after deploy** — almost always a stale cache or perms. Over SSH:
  `php artisan optimize:clear` and `chmod -R 775 storage bootstrap/cache`.

## Moving to another domain

The site answers on one address, `PRIMARY_URL` in `.github/workflows/deploy.yml`.
Each deploy writes it to the server `.env` as `PRIMARY_URL` and `APP_URL`, and
`app/Http/Middleware/PrimaryHost.php` does the rest: every link the site writes
uses that address, and a request on any other host that serves this folder
(www, the older domain) gets a permanent redirect to the same page.

Moving is two deploys, in this order:

1. **Make the new domain serve this folder.** Add the domain to the same
   hosting account (hPanel creates `~/domains/<domain>/public_html` with a
   placeholder page) and list it in `ALIAS_DOMAINS` in the workflow. The deploy
   runs `scripts/link-alias.sh`, which sets that placeholder folder aside and
   links it to `DEPLOY_PATH`. Check: `https://<new-domain>/ar` now redirects to
   the current address. That proves the new domain reaches this install.
2. **Switch.** Change `PRIMARY_URL` to the new domain and push. The deploy
   first asks the new address for `robots.txt` and refuses to continue if this
   site does not answer there, so a typo or an unlinked domain cannot take the
   site down. After it, the old address redirects every page to the new one.

Then, outside the code: add the new domain in Google Search Console and Bing
Webmaster Tools, submit `sitemap.xml` there, use Search Console's "Change of
address" on the old property, and update the links that point at the old
address (LinkedIn, GitLab, client-site credits).

Share pictures have the address drawn into them: add a pair for the new host
under `share_images` in `config/portfolio.php`.

The new domain may sit behind the host's CDN, which keeps a redirect it has
seen for about five minutes. The site's own host redirect is sent `no-store`
for that reason, and the deploy's checks add a query so they never read a
stored copy. If pages bounce between the two addresses right after a switch,
flush the CDN cache in hPanel or wait those few minutes.

To undo the link: `rm ~/domains/<domain>/public_html && mv ~/domains/<domain>/public_html.before-alias ~/domains/<domain>/public_html`.
To undo the switch: set `PRIMARY_URL` back and push.
