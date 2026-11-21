# Design system

The version the owner settled on, after several that he rejected (a paper
look, a mosaic wall, an editorial page with glossy 3D pieces, a full 3D room).
What worked each time was asking him with concrete choices, building a proof
he could open on his phone, and following his short corrections.

## The board (the chosen direction)

The owner chose the flat version over the 3D one, and then steered it:

- use the logo's own colours;
- draw in the logo's *language*, not the logo itself ("I did not mean use the
  whole logo as it is: work in the same design, but you are free with the
  shape and length of the lines");
- add a touch of UML;
- show only the first screenshot of each client site;
- place and format the text better;
- make the first section less boring (he suggested something being typed);
- apply the same look to every page of the site.

So the home page is one flat drawing and a camera that travels it
(`motion/board.js`, Canvas 2D, no library; view `home/board.blade.php`), and
every other page uses the same kit in HTML and CSS.

### The language (rules for anything drawn, on the canvas or in CSS)

- **Colours:** the logo's navy `#0f172a` and cyan (`--brand-primary`), white
  for text. Nothing else. No light sections anywhere.
- **Line:** one weight. Dashed (square dashes) means planned; solid means
  built.
- **Corners:** cut at 45°. Boxes are cut boxes (`cutBox`, `.framed`), big
  buttons and numbers are octagons. Nothing is rounded except dots and rings.
- **Marks from the logo:** the filled dot (the pen, a start), the ring (a
  port, an end), the solid arrowhead (direction).
- **UML, where it means something:** notes with a folded corner for
  questions, an actor for the founder, a diamond for the decision, a final
  state (ring with a dot) for launch, a class box for anything described
  (projects, services), a dashed realization arrow from a built window to the
  card that specifies it.
- **Composition is free.** Do not reproduce the logo's layout.
- **One screenshot per project** (and its phone).

### The home page, in the order the camera meets it

| Part | What happens |
|---|---|
| Hero | Beside the headline, a loop: a founder's sentence is typed into a prompt, breaks into three or four numbered octagons, and they become a small window that is built from a real project's screenshot. Four ideas, four real projects (`site.b.ideas`). |
| Roadmap | The camera goes down a line. Each of the four steps is an octagon on it, with the step on one side and the founder's question it answers in a UML note on the other. A diamond on the 45° jog: "your approval first". Two statements are pinned as margin notes. |
| Projects | A staircase of four windows. First a pull-back that shows all four as drawings, then one stop each: the window is built (Sajjah in tiles, Shattib from its own blueprint, Rouaa as three tinted phones, Nour by lamplight) and a class card describes it. |
| End | A final state: launch. Then the camera pulls back over the whole sheet while prices, method and questions are read, and the closing headline sits over the whole route. |

A pen travels the route; scroll is the timeline (`motion/timeline.js`). Keys
are made in `buildKeys()` from the viewport and joined with a monotone curve
so a framed shot is never overshot. Words on the sheet come from the lang
files through `data-board` and exist as HTML too.

### The kit (every other page)

In `app.css`, under "THE KIT":

- the grid behind every page (`body`);
- `.framed` (a cut-corner frame; `.framed__in` clips pictures the same way);
- `.uml` (class card: head / attributes / operations / foot), used for
  projects on the board and for services;
- `<x-window>` / `.win` (a browser window with three rings), used on the work
  index and project pages;
- `<x-trace>` / `.trace` (dot, dashed line, arrowhead, ring), which closes
  every page head (`<x-page-head>`);
- `.steps` (octagon numbers on a dashed line), `.cta` (cut corners), `.rbtn`
  (octagons), `.opt` (cut chips).

### Text

- Hero: status line, headline, lead and actions in one column on the reading
  side; the loop plays on the other side (under the text on a phone).
- Statements are margin notes with a cyan rule; station text lives in the
  class card. A soft shade (`.board__shade`, `--shade`) sits under them so
  text never lies on line-work. No glow shadows.

### Still to settle

- The earlier 3D take and the Tawazoun load toy were deleted on the owner's
  say (2026-11-21), with the three.js dependency.
- The founder-facing copy (`site.b.*`), prices and FAQ promises are proposals.
- He does not want videos of the site: send stills and the link.

## Search and sharing

- Every page's `<head>`, share tags and JSON-LD come from `components/layout.blade.php` and `app/Support/Seo/JsonLd.php`. A page adds its own nodes through `PageMeta` (`schema`, `image`).
- Prices in structured data, the FAQ and `llms.txt` are read from `config/pricing.php`. Never type a price into copy: use the tokens `faq_items()` fills in.
- Mark questions up as FAQPage only on pages where the visitor can read them.
- A project cover `name.webp` may have a lighter sibling `name-s.webp` (1600px). `Project::coverSmall()` finds it; the board and `<x-window>` use it on small screens. Dashboard uploads simply fall back to the cover.
- The board asks for the hero's pictures at once and for the stations' only after the page has loaded.

## Service pages

One page per offer in `config/pricing.php`, at `/{locale}/services/{slug}` (`pages/service-show.blade.php`, `App\Support\ServicePages`).

- Words: `lang/{ar,en}/service_pages.php`. Never type a price or a duration there: `:from` and `:time` are filled from the price guide. Every answer is a promise, so nothing goes in that the owner has not confirmed.
- Order of the page: head (the phrase people search for), the short answer beside the service's class card, who it is for and who it is not, what is handed over, real work of that kind, the calculator preset to the service, the four steps, its own questions, the other five.
- Proof: `config/service_pages.php` lists projects per service. A service with none shows the method section instead, never an empty heading.
- Structured data: a `Service` node with the starting price, a three-step trail, and FAQPage for the page's own questions only.
- Long titles use `.page-head--long`; two-column lists use `.row--2`.
- Each service has its own headings (`fit_title`, `gets_title`, `proof_title`, `estimate_title`, `faq_title`) that name the service, several of them as questions. `proof_lead` says exactly what the projects shown are: never imply work that is not on the page.
- The line under the short answer names who does the work and when the prices were last checked (`pricing.reviewed`, also the `dateModified` of the price pages and their `lastmod` in the sitemap). Change that date when the prices are reviewed.
- Durations in Arabic are written as people say them (`weeks_span()` in PHP, the same rule in `pages/services.js`): never a number followed by one fixed word for "weeks".

## Server and crawling

- On the server the project sits in the web root and a rule there passes requests into `public/`, so inside `public/.htaccess` a path may start with `/public`. No redirect may keep that prefix (the site would exist twice). The rules were checked on a local Apache laid out the same way; do that again before changing them.
- One address: `PRIMARY_URL` in the deploy workflow. `PrimaryHost` middleware redirects every other host that serves the folder and makes every generated link use it. Moving to a new domain is a change of that one line (steps in `deploy/CICD.md`). Never hardcode the site's address anywhere; share pictures, which have it drawn in, are chosen per host by `share_image()`.
- One host (www redirects), one `x-default` per language pair (the first of `app.supported_locales`), and in `robots.txt` every group that names a crawler repeats the dashboard rule, because a crawler follows only its own group.
- A missing page is `errors/404.blade.php`: it reads the language from the address, since no route (and so no locale middleware) ran.
- Questions are `h3` inside `summary`. The share picture carries a headline, so English pages use `og-en.png`.

## Checking the board

`scratchpad/tools/film.mjs` drives `window.__journey.seek(u, time)` in headless
Chrome and saves frames, which is how stills at an exact point of the scroll
are taken. The owner does not want videos: send stills and the link.

## Lessons kept

- Never let anything overflow the page sideways: a very wide child with
  `overflow: visible` once made the compositor drop the whole page on a real
  GPU.
- Component CSS lives in `@layer components` so utilities such as `hidden`
  still win.
- Headless Chrome (software GL) is fine for layout and for the film; check the
  real GPU in the app's browser pane.
