# PRODUCT.md

> Context for design work. Read by the impeccable skill (`.agents/skills/impeccable`).

## What this is
A bilingual (Arabic / English, RTL-first) **portfolio** for **Baraa Aldomani**, a freelance software engineer based in Riyadh, Saudi Arabia. Marketing surface — **design IS the product** (impeccable **brand** register).

## Audience
Prospective clients (founders, SMEs, government/agency contacts in KSA) and technical recruiters. They scan fast and judge credibility from the first fold.

## Brand voice
Talks to the client about their problem, in plain words. Shows proof instead of claiming skill. Calm, concrete, never boastful.

## Surfaces
Home, Services, Projects (+ case-study detail), About, Contact, Blog (stub). Content is locale-driven (`lang/{en,ar}`), projects are DB-backed (`App\Models\Project`), contact details live in `config/portfolio.php`.

## Design
Deep navy and the logo's cyan. A living mosaic wall of octagon tiles in the
hero, and a separate "world" (palette, layout, interaction) for every project.
Real screenshots, public prices, a project calculator. See `DESIGN.md`.

## Hard constraints
- **Bilingual parity + RTL**: every string in both `lang/en` and `lang/ar`; logical properties only.
- **Zero em-dashes** in visible copy (design-taste rule): comma/period, Arabic `،`.
- **Accessibility**: content visible without JS (motion gated behind `html.js`), `prefers-reduced-motion` honored, real alt/aria, WCAG-AA contrast.
- Front-end deps are deliberate and few: GSAP (ScrollTrigger), Lenis, Fontsource fonts. No UI framework.
