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
| `PHP_BIN`         | `php8.3`                                               | Optional. Only if plain `php` isn't 8.3+ on the server. |

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

## Troubleshooting

- **`Permission denied (publickey)`** — public key isn't imported on Hostinger,
  or `SSH_PORT`/`SSH_USER`/`SSH_HOST` is wrong. Re-test the raw
  `ssh -p … -i …` command from step 2.
- **`Host key verification failed`** — the runner couldn't `ssh-keyscan` the
  host; check `SSH_HOST`/`SSH_PORT`.
- **`php: command not found` or wrong PHP version** — set the `PHP_BIN` secret to
  `php8.3`. Confirm with `php8.3 -v` over SSH.
- **`rsync: command not found` on the server** — rare on Hostinger. If it
  happens, switch the transfer to SFTP (ask and I'll adjust the workflow).
- **500 after deploy** — almost always a stale cache or perms. Over SSH:
  `php artisan optimize:clear` and `chmod -R 775 storage bootstrap/cache`.
