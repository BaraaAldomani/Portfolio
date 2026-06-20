# Automated deploy: GitLab CI/CD → Hostinger (SSH)

Push to your default branch and the site deploys itself. GitLab builds
`vendor/` and `public/build/`, rsyncs the app to Hostinger over SSH, then runs
`migrate` + the cache commands on the server. Your production `.env` and any
images you uploaded on the server are **never overwritten** (rsync excludes
`.env` and never deletes remote files).

The pipeline lives in [`.gitlab-ci.yml`](../.gitlab-ci.yml).

---

## One-time setup

### 1. Generate a deploy SSH key (on your machine)

```bash
ssh-keygen -t ed25519 -C "gitlab-deploy" -f ./hostinger_deploy -N ""
```

This makes two files: `hostinger_deploy` (private) and `hostinger_deploy.pub`
(public). Do **not** commit them.

### 2. Add the public key to Hostinger

hPanel → **Advanced → SSH Access**. Make sure SSH is **enabled** and note the
**IP/host**, **username** (e.g. `u123456789`), and **port** (Hostinger shared
hosting is usually **65002**, not 22). Paste the contents of
`hostinger_deploy.pub` into the **SSH Keys** box (or, via terminal, append it to
`~/.ssh/authorized_keys`).

Test it from your machine:

```bash
ssh -p 65002 u123456789@YOUR_HOST -i ./hostinger_deploy
```

You should land in your home dir without a password prompt.

### 3. Find your deploy path

Over that SSH session:

```bash
pwd                                   # e.g. /home/u123456789
ls domains/                           # find your domain folder
# DEPLOY_PATH is the folder that contains artisan, app, public, vendor …
# typically: /home/u123456789/domains/yourdomain.com/public_html
```

### 4. Make sure the app exists on the server once

The pipeline **updates** an existing install; it does not bootstrap a brand-new
one. Do the very first deploy the manual way (see [`DEPLOY.md`](DEPLOY.md)): the
zip, the `.env` with real DB credentials, `migrate --seed`, document root → set
to `…/public`. After that, every push deploys automatically.

> The server `.env` is the source of truth and is excluded from rsync. Edit it
> directly on the server when secrets change.

### 5. Add the CI/CD variables in GitLab

GitLab project → **Settings → CI/CD → Variables → Add variable**. Mark each
**Protected** (and **Masked** where allowed):

| Key               | Value (example)                                          | Notes |
|-------------------|---------------------------------------------------------|-------|
| `SSH_PRIVATE_KEY` | *(paste the whole `hostinger_deploy` file)*             | Type **Variable**, not Masked (multi-line). |
| `SSH_HOST`        | `123.45.67.89` or your server hostname                  | From hPanel → SSH Access. |
| `SSH_USER`        | `u123456789`                                            | |
| `SSH_PORT`        | `65002`                                                 | Check hPanel; usually 65002. |
| `DEPLOY_PATH`     | `/home/u123456789/domains/yourdomain.com/public_html`   | The folder containing `artisan`. |
| `PHP_BIN`         | `php8.3`                                                 | Optional. Only if plain `php` isn't 8.3+ on the server. |
| `APP_URL`         | `https://yourdomain.com`                                | Optional; just labels the deploy in GitLab. |

`SSH_PRIVATE_KEY` must include the `-----BEGIN …-----` / `-----END …-----`
lines and a trailing newline.

---

## Deploy

```bash
git push origin <default-branch>
```

Watch it in GitLab → **Build → Pipelines**. The `deploy:production` job only
runs on the default branch, so feature branches build/test without deploying.

### Want a manual confirmation gate instead of auto-deploy?

In `.gitlab-ci.yml`, under `deploy:production` → `rules`, add `when: manual`:

```yaml
  rules:
    - if: '$CI_COMMIT_BRANCH == $CI_DEFAULT_BRANCH'
      when: manual
```

Then the pipeline waits for you to click **▶ play** on the deploy job.

---

## Troubleshooting

- **`Permission denied (publickey)`** — public key isn't in the server's
  `authorized_keys`, or `SSH_PORT`/`SSH_USER`/`SSH_HOST` is wrong. Re-test the
  raw `ssh -p … -i …` command from step 2.
- **`Host key verification failed`** — the runner couldn't `ssh-keyscan` the
  host; check `SSH_HOST`/`SSH_PORT`.
- **`php: command not found` or wrong PHP version** — set `PHP_BIN` to `php8.3`.
  Confirm with `php8.3 -v` over SSH.
- **`rsync: command not found` on the server** — rare on Hostinger. If it
  happens, switch the transfer to `lftp`/SFTP (ask and I'll adjust the job).
- **500 after deploy** — almost always a stale cache or perms. Over SSH:
  `php artisan optimize:clear` and `chmod -R 775 storage bootstrap/cache`.
