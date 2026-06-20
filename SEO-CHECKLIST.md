# SEO & Launch Checklist

Built-in already (no action needed):

- [x] Unique `<title>` + meta description per page **per language**
- [x] `hreflang` (ar/en + `x-default`) and canonical URLs on every page
- [x] Open Graph + Twitter card tags + default OG image (`public/images/og-default.png`)
- [x] JSON-LD: Person, ProfessionalService (Riyadh, areaServed: Saudi Arabia), WebSite
- [x] `sitemap.xml` (both languages, with hreflang alternates) at `/sitemap.xml`
- [x] `robots.txt` at `/robots.txt` (points to the sitemap)
- [x] `llms.txt` at `/llms.txt` (for AI crawlers / AI search assistants)
- [x] Semantic HTML5, exactly one `<h1>` per page, clean localized slugs
- [x] Lazy-friendly, minimal JS, font preloading via Vite, transform/opacity-only animations

---

## Post-launch tasks (do these after the domain is live)

### Search engines
- [ ] **Google Search Console** — add both `https://domain/ar` and `/en`, verify via DNS TXT, submit `/sitemap.xml`.
- [ ] **Bing Webmaster Tools** — add the site, import from GSC, submit the sitemap.
- [ ] Request indexing for the home pages of both languages.
- [ ] Confirm `hreflang` has no errors in GSC's International Targeting report.

### Business presence
- [ ] **Google Business Profile** — create a profile (Riyadh, service-area business), category "Software company / Website designer", link the site.
- [ ] **LinkedIn** — add the site URL to the profile; publish a launch post linking it (backlink + referral traffic).
- [ ] **GitLab** profile README — link the portfolio.

### Backlinks & directories
- [ ] List on relevant freelancer/agency directories (Mostaql, Bahr, Clutch, GoodFirms, etc.).
- [ ] Saudi/Gulf tech directories and local business listings.
- [ ] Ask past clients/colleagues for a LinkedIn recommendation + a backlink where natural.

### Verification & quality
- [ ] Run **Lighthouse** (mobile + desktop) on `/ar` and `/en` — target 95+ across all metrics.
- [ ] Validate JSON-LD with the **Rich Results Test**.
- [ ] Validate OG/Twitter cards (LinkedIn Post Inspector, Twitter Card Validator).
- [ ] Test `prefers-reduced-motion` and RTL mirroring on real devices.
- [ ] Set up privacy-friendly analytics (e.g. Plausible / Umami) if desired.

### Target keywords (used naturally in copy & meta)
- freelance software developer Riyadh · freelance software engineer Riyadh
- Laravel developer Saudi Arabia
- مطور مواقع مستقل الرياض · مبرمج مستقل الرياض
