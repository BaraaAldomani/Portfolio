# PRODUCT.md

> Context for design work. Read by the impeccable skill (`.agents/skills/impeccable`).

## What this is
A bilingual (Arabic / English, RTL-first) **portfolio** for **Baraa Aldomani**, a freelance software engineer based in Riyadh, Saudi Arabia. Marketing surface — **design IS the product** (impeccable **brand** register).

## Audience
Prospective clients (founders, SMEs, government/agency contacts in KSA) and technical recruiters. They scan fast and judge credibility from the first fold.

## Brand voice
Technical, precise, calm, confident. Concrete over hype. Engineering authenticity, not startup fluff.

## Surfaces
Home, Services, Projects (+ case-study detail), About, Contact, Blog (stub). Content is locale-driven (`lang/{en,ar}`), projects are DB-backed (`App\Models\Project`), contact details live in `config/portfolio.php`.

## Design versions (URL-switchable)
The site ships **two parallel design systems**, selected by a `{version}` URL segment and a nav toggle:

- **v1** — `/v1/...` (default for now). Light, indigo + teal, aurora/SaaS, animated terminal. The original. Untouched.
- **v2** — `/v2/...`. **"Engineering terminal / blueprint dark"**: near-black base, off-white ink, ONE electric-lime accent; Bricolage Grotesque display + JetBrains Mono (Cairo for Arabic); CLI/manifest motifs; orchestrated first-load motion with custom easing. See `DESIGN.md`.

`/` and bare `/{locale}` redirect to **v1**. v2 is `noindex` while it is a preview; flip the default in `routes/web.php` when ready. Mechanics: `App\Http\Middleware\SetVersion` prepends `resources/views/{version}` to the view finder, so v2 overrides components/pages and falls back to v1 where absent.

## Hard constraints
- **Bilingual parity + RTL**: every string in both `lang/en` and `lang/ar`; logical properties only.
- **Zero em-dashes** in visible copy (design-taste rule): comma/period, Arabic `،`.
- **Accessibility**: content visible without JS (motion gated behind `html.js`), `prefers-reduced-motion` honored, real alt/aria, WCAG-AA contrast.
- No new runtime deps — vanilla JS + Tailwind v4.
