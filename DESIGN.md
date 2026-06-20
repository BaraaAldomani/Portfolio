# DESIGN.md — Baraa Aldomani Portfolio (current live design)

A complete design + motion specification for the site, written so another design / UI-UX agent can fully understand, reproduce, or evolve it. It documents the **single live design** (an elegant dark theme). The old `v1` (light) and `v2` (terminal-lime) systems were removed; ignore any references to versions elsewhere.

---

## 1. Product context

- **Who:** Baraa Aldomani, a **Software Engineer** (backend-focused) based in Riyadh, Saudi Arabia.
- **Goal of the site:** position him as a serious, hireable **Software Engineer** for both **companies** (full-time) and **freelance** clients. It should read as "senior, reliable, builds systems that scale," not "junior dev."
- **Bilingual, RTL-first:** Arabic (`ar`, primary, RTL) and English (`en`, LTR). Both are first-class; Arabic is the default.
- **No project screenshots by design:** the real work is government / NDA / older, so projects are sold through **outcomes and metrics**, not UI shots. Cover art is abstract/branded.
- **Tone:** confident, calm, precise, engineering-credible. Not loud, not "salesy."

### Page inventory
`/{locale}` routes: **Home**, **Services**, **Projects** (index), **Project detail**, **About**, **Contact**, (Blog stub). `/` → `/ar`.

---

## 2. Brand identity

- **Logo:** a custom "B" mark made of octagon/hexagon + circular nodes (a UML/graph motif). Files: `public/images/logo.svg` (and `logo.png` for the favicon).
- **Primary brand color:** **`#24abe3`** — a clear cyan-blue taken directly from the logo. This is THE brand color; everything keys off it.
- **Secondary brand context (Rakeez founder section only):** Rakeez palette `#074173` / `#1679AB` / `#5DEBD7`, shown on a white card.
- **Personality:** technical blueprint / architecture diagram. The signature motif (see §10) is a faint UML + Git-graph network drawn behind the whole page.

---

## 3. Theming architecture (how color works)

The whole site derives every color from a tiny set of brand variables via CSS `color-mix(in oklab, …)`. Blade never uses raw hex — only **semantic Tailwind utilities** (`bg-surface`, `text-ink`, `text-muted`, `border-line`, `bg-primary`, `text-primary-700`, `*-on-dark`).

- `resources/css/tokens.css` defines the *architecture* (scales + semantic aliases) for a light base.
- `resources/css/v3.css` is loaded **after** `app.css` and **overrides the variables** to the live dark theme. This is the file that defines the real, current look.
- Tailwind v4 maps the variables to utilities via `@theme inline` in `app.css`.

**To rebrand:** change `--brand-primary` (and the surface values) in `v3.css`; the full ramp, links, glows, and the background watermark recolor automatically.

---

## 4. Color system (live values)

### Brand blue ramp (tuned so it stays legible on dark)
| Token | Value | Use |
|---|---|---|
| `--brand-primary` / `-600` | `#24abe3` | brand, primary buttons, key accents |
| `--brand-primary-700` | `#5bc0ee` | **links / accent text on dark** (lighter on purpose) |
| `--brand-primary-400` | `#5cc1ee` | the background watermark color |
| `-300 / -500 / -800 / -900` | `#8ad4f3 / #36b1e6 / #86d0f2 / #b6e3f8` | hovers, subtle fills |
| `-50 / -100 / -200` | dark-tinted mixes of `#24abe3` onto `#0e141c` | chip/badge backgrounds |

> Note the inversion: on a dark UI, the **higher** primary numbers are **lighter**. Links use `primary-700 = #5bc0ee`.

### Surfaces & ink (soft navy, never pure black)
| Token | Value | Use |
|---|---|---|
| `--brand-surface` | `#0e141c` | page background |
| `--brand-surface-muted` | `#131b25` | alternating sections / chips |
| `--brand-surface-dark` | `#0a0f15` | deepest sections |
| `--brand-surface-dark-soft` | `#18222e` | cards on dark |
| `--brand-text` | `#e9eef4` | primary text |
| `--brand-text-muted` | `#9caebf` | secondary text |
| `--brand-line` | `rgba(255,255,255,.11)` | borders / hairlines |
| selection | `--brand-primary` @ 40% | text selection |
| primary button text | `#08121b` | dark ink on cyan buttons |

Contrast: body text `#e9eef4` on `#0e141c` is ~13:1; muted `#9caebf` ~6:1. Keep new colors above 4.5:1.

---

## 5. Typography

- **Sans (UI + body):** `'Hanken Grotesk'` (Latin) with `'Cairo'` (Arabic) fallback. Weights 400/500/600/700/800.
- **Mono:** `'JetBrains Mono'` (+ Cairo) 400/500 — used for dates, eyebrow labels, periods, code, the terminal.
- Fonts load from **Bunny Fonts** via `<link>` in the layout: `hanken-grotesk`, `jetbrains-mono`, `cairo`.
- **Headings:** `letter-spacing: -0.025em`, `text-wrap: balance`. Body `p` uses `text-wrap: pretty`.
- **Scale (Tailwind):** hero `h1` = `text-4xl → sm:text-5xl → lg:text-6xl`, weight 800, `leading-[1.08]`. Section `h2` = `text-2xl → sm:text-3xl/4xl`. Body `text-lg`/`text-base` with `leading-relaxed`. Eyebrows = `text-xs/sm`, bold, often `tracking-wide`, `primary-700`.

### Arabic typography rules (important)
Arabic is cursive/connected — Latin tracking breaks it. On `html[lang='ar']`:
- Heading negative tracking is reset to `letter-spacing: 0` (also any `tracking-*` utility).
- Extra line spacing: `body 2.05`, `p/li 2.2`, `.leading-relaxed 2.3`, `h1 1.4`, `h2/h3 1.55`.
- `word-spacing: 0.06em` on body/p/li/a.

---

## 6. Layout & spacing system

Shared pages use a single `<x-section>` wrapper (`resources/views/components/section.blade.php`):
- **width:** `prose=max-w-2xl` · `narrow=max-w-3xl` · `default=max-w-6xl` · `wide=max-w-7xl` · `full`.
- **bg:** `surface` · `muted` · `dark` (adds the aurora mesh + on-dark text) · `none`.
- **spacing (vertical rhythm):** `tight = py-14 sm:py-20` · `default = py-20 sm:py-28` · `hero = py-24 sm:py-32` · `flush`.
- **container:** always `mx-auto w-full px-5 sm:px-6 lg:px-8` + the width cap.

The bespoke Home/About sections use the same scale directly (`max-w-6xl/7xl`, `px-5 sm:px-8`, `py-16 sm:py-24`). Cards standardize on `p-6 sm:p-8`, radius `rounded-2xl/3xl`, `border border-line`. Gap rhythm: content-after-heading `mt-12`.

---

## 7. Bilingual / RTL behavior

- `dir="rtl"` for Arabic; all spacing uses **logical properties** (`ms-/me-`, `ps-/pe-`, `start-/end-`, `inset-inline`) so the layout mirrors correctly.
- Directional scroll-reveals use `.reveal-start` (slides in from the inline-start, flips with direction).
- Icons that imply direction use `rtl:-scale-x-100`.
- The hero **portrait is mirrored on English only** (`scaleX(-1)` on the wrapper) so the subject faces into the headline; normal on Arabic.

---

## 8. Components

- **Top nav** (`components/site-nav.blade.php`): sticky, `border-b`, `bg-surface/80 backdrop-blur-md`. Left: logo (`h-9`) + name + "Software Engineer · Riyadh". Center: text links with an animated underline (`.nav-link`). Right: language toggle + **"Let's talk"** primary pill. Mobile: hamburger → slide-down drawer (`#nav-toggle`/`#mobile-menu`).
- **Footer** (`components/site-footer.blade.php`): a CTA band (big headline + two CTAs: magnetic primary + email ghost), then a 4-col block (brand+tagline / nav / social channels+email), then a bottom bar (`© {year}` + back-to-top).
- **Buttons / CTAs:** primary = `rounded-full bg-primary text-[#08121b]` with `shadow-primary-600/25`, hover `bg-primary-400`; secondary/ghost = `border border-line bg-surface text-ink`, hover `border-primary-300`. Key CTAs add `.js-magnetic`; all use `.btn-press` (`scale(0.97)` on press).
- **Eyebrow / availability pill:** `rounded-full border border-primary-200 bg-primary-50 text-primary-700`, with an optional **pulsing dot** ("Open to roles & freelance work").
- **Project card** (`components/project-card.blade.php`): `.js-tilt .glow-border`, a 16:10 **cover image (DB-driven `Project::coverImage()`)**, hover gradient wash + circular arrow that slides up, then sector eyebrow, title, summary, highlight chips, "Case study" link.
- **Experience timeline** ("Where I have worked"): a vertical `border-s-2` line with `.v3-node` dots that **scale 1.25 + fill cyan on row hover**; role · org (org is a link), period in mono, blurb.
- **Focus rows** ("What I do best"): `.v3-row` list rows; on hover the row gets a faint cyan bg and a vertical `.v3-row__bar` scales in.
- **Rakeez founder block** (About): the one place using a **white card** with the Rakeez logo + secondary palette, badge "Founder".
- **Stats / metrics:** big number + `[data-counter]` count-up + small label (e.g., "4+ Years building software").
- **Terminal** (`.js-terminal`): a looping typewriter "CLI" component (available; used selectively).
- **Forms (Contact):** labeled inputs on dark, honeypot field, server validation states, success flash. The form **stores messages to the database** (no email is sent).

---

## 9. Page layouts (structure)

- **Home:** Hero (2-col: copy + grayscale **portrait** with floating "4+ years" badge) → "Engineering that holds up under real load" proof + metrics → "What I do best" focus list → "Real projects, measurable results" project grid.
- **About:** Intro (copy + photo) → **Where I have worked** timeline → **Rakeez (Founder)** highlight → Capabilities + Education.
- **Services:** hero → alternating service feature rows → impact stats band → process timeline (`.js-draw`) → tech-stack marquee (`.js-marquee`) → CTA.
- **Projects:** hero → staggered grid of project cards.
- **Project detail:** back link → header (sector, title, summary, highlight chips) → cover banner → numbered **Problem / Solution / Result** → CTA.
- **Contact:** intro + contact-method cards + the form.

> The Services / Projects / Contact pages were originally authored for the earlier light theme and are reused under the dark theme (they recolor via tokens). They work, but they are the **strongest candidates for bespoke dark-native redesign** (see §13).

---

## 10. Signature background — static UML / Git watermark

Built by `initNetbg()` in `resources/js/v3.js`; styled by `.netbg` in `v3.css`.

- A **full-document SVG overlay** (`position:absolute; inset:0; z-index:-1`) generated in JS, sized to the whole page.
- **Motif:** octagon "UML class" nodes, empty rings, solid commit dots, a vertical solid "git spine", dashed git-branch curves, and association **arrows**.
- **Perimeter-anchored connectors (key quality detail):** every line attaches to the *edge* of its shapes — circle radius for dots/rings, exact **ray/polygon intersection** for octagons — so a line **never pierces a node** (Lucidchart/draw.io quality).
- **Responsive:** density scales with viewport width (lanes 2 → 5), shapes shrink on mobile; regenerates on resize / height change (debounced, `ResizeObserver`).
- **Faintness:** `color: --brand-primary-400`, **`opacity: 0.04`** — a premium watermark that never competes with text. (If a future design wants it more present, raise this one value.)
- **Static** (no scroll animation, no per-frame work) by deliberate choice.

---

## 11. Motion system (the full catalog)

One shared engine in `resources/js/animations.js` (`initAnimations()`), plus v3-specific add-ons in `resources/js/v3.js`. **Every effect animates only `transform`/`opacity`** (GPU, zero layout shift).

### Easings & timings
- `--v3-ease: cubic-bezier(0.16, 1, 0.3, 1)` (smooth expo-out, the house curve).
- `--v3-ease-out: cubic-bezier(0.23, 1, 0.32, 1)` (snappier).
- SVG draw: `cubic-bezier(0.22, 1, 0.36, 1)`.

### Shared engine hooks (attribute/class → effect)
| Hook | Effect | Key timing |
|---|---|---|
| `.reveal` / `.reveal-start` | scroll fade+rise / fade+slide-from-inline-start | opacity .7s / transform .85s; IO threshold .15 |
| `[data-stagger]` | stagger children's reveals | 90ms step |
| `[data-reveal-text]` | headline reveals **word-by-word** in view | 55ms per word |
| `[data-counter]` | count-up to number (locale-formatted) | 1400ms, cubic ease-out |
| `[data-parallax]` (`--parallax-speed`) | subtle scroll translate3d | default speed 0.12 |
| `.gradient-text` | animated gradient sweep on a heading | CSS only |
| `.js-terminal` | looping typewriter CLI | ~14–32ms/char |
| `.js-draw` | SVG stroke draws in (dash offset) | 1.2s |
| `.js-tilt` (+`.glow-border`) | 3D pointer tilt ±8°, cursor glow via `--mx/--my` | pointer |
| `.js-magnetic` | element pulls ~22% toward cursor | pointer |
| `.js-spotlight` | section radial glow follows cursor | pointer |
| `.js-marquee` | infinite horizontal strip | CSS only |
| `#scroll-progress` | top reading-progress bar (`scaleX`) | rAF |
| `#nav-toggle`/`#mobile-menu` | mobile drawer | — |

### v3 add-ons (in `v3.js` / `v3.css`)
- **On-load choreography** `[data-load]/[data-load-item]`: items fade + rise (`translateY(22px)`) in sequence on first paint; 90ms steps; opacity .8s / transform .9s.
- **Image clip-reveal** `[data-img-reveal]`: image unmasks `clip-path: inset(0 0 100% 0) → 0` while scaling `1.08 → 1`; clip 1s / scale 1.4s. Fires via IntersectionObserver.
- **Cursor glow** `#cursor-glow`: a 420px radial cyan glow that eases toward the cursor (lerp 0.12); fine-pointer only.
- **Hover lift / press:** `.v3-lift` raises `translateY(-4px)`; `.v3-press`/`.btn-press` press to `scale(0.97)` (.16s).
- **Grayscale portrait → color on hover:** `.v3-photo` is `grayscale(1)`, returns to color on hover (.6s).
- **Timeline node + focus-row bar:** see §8.

---

## 12. Accessibility & performance (non-negotiable)

- **`prefers-reduced-motion`:** all decorative motion is disabled; on-load/reveal items show immediately; terminal renders statically; cursor glow hidden. Content is never gated behind motion.
- **No-JS / crawlers:** nothing is hidden without `html.js`; full content renders.
- **Touch devices:** tilt / magnetic / spotlight are skipped; CSS `:active` tap states remain.
- **Layout stability:** all `<img>` have explicit `width`/`height`, `loading="lazy"`, `decoding="async"`, real localized `alt`.
- **Semantics:** landmark elements, a skip-to-content link, `aria-current` on active nav, `aria-expanded` on the menu, focus-visible outlines.
- **SEO:** per-page title/description, canonical + `hreflang` alternates, Open Graph, `Person` JSON-LD, sitemap/robots/llms.

---

## 13. Constraints + where the design can go next

**Hard constraints (please respect):**
- Tech: **Laravel Blade + Tailwind v4 + vanilla JS**. **No new runtime dependencies** (no React, no GSAP, no animation libs). Motion stays transform/opacity-only and reduced-motion-safe.
- All color via tokens/semantic utilities — **never hardcode hex** in markup.
- **No em-dashes** in copy (use commas/periods); keep the bilingual parity (every string in both `ar` + `en`).
- Keep the brand cyan `#24abe3`, the soft-navy dark surfaces, and the UML/Git watermark identity.

**Open opportunities for a UI/UX pass:**
1. **Services / Projects / Contact** pages are reused from the older light theme — they deserve bespoke, dark-native layouts (stronger hierarchy, better use of the dark surfaces and the watermark).
2. **Project cards** currently use abstract gradient covers (no screenshots, by design) — explore more distinctive, metric-forward card designs that sell outcomes.
3. **Mobile**: tighten the hero (portrait stacking), nav drawer polish, and density of the watermark on small screens.
4. **Empty/loading/error states**, form success microcopy, and the Blog (currently a stub).
5. Consider a refined **type scale** and more deliberate vertical rhythm across the bespoke sections (Home/About don't use `<x-section>`; unifying them would help).

---

## 14. File map (for implementers)

- Tokens / theme: `resources/css/tokens.css` (architecture), `resources/css/v3.css` (live dark overrides + v3 motion).
- Base utilities + aurora/marquee/gradient/terminal CSS + Tailwind `@theme`: `resources/css/app.css`.
- Motion engine: `resources/js/animations.js`; v3 add-ons + watermark: `resources/js/v3.js`.
- Shell: `resources/views/components/{layout,site-nav,site-footer,section,project-card,…}.blade.php`.
- Pages: `resources/views/pages/{home,about,services,projects,project-show,contact}.blade.php`.
- Copy: `lang/{en,ar}/*.php` (keep parity).
- Brand assets: `public/images/{logo.svg,logo.png,rakeez-logo.svg, baraa-portrait.png, baraa-about.jpg, projects/*.svg}`.
