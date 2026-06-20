# Baraa Aldomani — Portfolio

Bilingual (Arabic-first / English) freelance portfolio for **Baraa Aldomani, Freelance Software Engineer** (Riyadh, Saudi Arabia).

- **Stack:** Laravel 13 · Blade (server-rendered) · Tailwind CSS v4 · Vite · PostgreSQL 16
- **No SPA, no heavy JS** — Alpine-free, one small vanilla animation utility
- **Bilingual** `/ar` (RTL, default) and `/en` (LTR) with full hreflang/SEO
- **Dockerized** dev environment (PHP 8.3-FPM, nginx, Postgres, Node)

---

## Local setup (Docker)

Prerequisites: Docker Desktop. (Optional) GNU Make for the shortcuts below.

```bash
cp .env.example .env

make up                       # build images + start app, nginx, db, node
make composer cmd="install"   # install PHP dependencies
make artisan cmd="key:generate"
make fresh                    # migrate + seed projects
make npm cmd="run build"      # build front-end assets (or use the live Vite dev server)
```

Open **http://localhost:8080** → it redirects to `/ar`.

> The `node` service runs the Vite dev server at http://localhost:5173 for HMR.
> For a production-like static build instead, run `make npm cmd="run build"`.

### Make shortcuts

| Command | Action |
| --- | --- |
| `make up` / `make down` | start / stop the stack |
| `make sh` | shell into the app container |
| `make artisan cmd="…"` | run an artisan command |
| `make composer cmd="…"` | run composer |
| `make npm cmd="…"` | run npm in the node container |
| `make migrate` / `make fresh` | migrate / migrate:fresh --seed |
| `make logs` / `make ps` | tail logs / list services |

No Make? Run the underlying `docker compose …` commands directly (see the `Makefile`).

---

## Project conventions

- **Colors:** every color is a CSS variable in [`resources/css/tokens.css`](resources/css/tokens.css). Edit the three `--brand-*` values at the top to rebrand the whole site — all shades, surfaces, and the hero aurora derive automatically. No raw hex lives in Blade or Tailwind config.
- **Strings:** all copy lives in [`lang/ar`](lang/ar) and [`lang/en`](lang/en) — never hardcoded in Blade.
- **Contact details / CV path:** centralized in [`config/portfolio.php`](config/portfolio.php).
- **Animations:** one shared, documented utility — [`resources/js/animations.js`](resources/js/animations.js) (scroll-reveal, counters, terminal, tilt, magnetic). Respects `prefers-reduced-motion` and is RTL-aware.
- **Code:** camelCase, slim controllers, small focused methods, no dead code.

### Replacing the CV

Drop the new file at `public/cv/baraa-aldomani-cv.pdf` (path configurable in `config/portfolio.php`).

### Regenerating the social (OG) image

```bash
docker compose exec app php scripts/generate-og-image.php
```

---

## Production deployment

Two supported paths:

### A) Managed (Forge / Ploi + VPS)

1. Provision a VPS (e.g. Hetzner/DigitalOcean) and connect it to **Laravel Forge** or **Ploi**.
2. Create a site, attach this Git repo, set the PHP version to **8.3** and the web root to `public/`.
3. Set environment variables (`APP_ENV=production`, `APP_DEBUG=false`, `APP_KEY`, `APP_URL=https://yourdomain`, and the `DB_*` for the managed Postgres).
4. Deploy script:
   ```bash
   composer install --no-dev --optimize-autoloader
   php artisan migrate --force
   php artisan db:seed --force        # first deploy only
   npm ci && npm run build
   php artisan config:cache && php artisan route:cache && php artisan view:cache
   ```
5. Put **Cloudflare** in front (proxied DNS), enable **Full (strict) SSL**, and let Forge/Ploi issue the Let's Encrypt certificate.

### B) Docker image

The [`Dockerfile`](Dockerfile) has a production-ready `prod` target (vendored deps, built assets, tuned opcache, runs as `www-data`).

```bash
docker build --target prod -t baraa-portfolio:latest .
```

Run it behind nginx/Traefik with a managed Postgres, terminate TLS at Cloudflare or the proxy, and run `php artisan migrate --force` on release.

See [`SEO-CHECKLIST.md`](SEO-CHECKLIST.md) for post-launch tasks.
