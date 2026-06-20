/**
 * animations.js — the site's ONE shared animation utility.
 *
 * Everything decorative on the site is driven from here; no page ships
 * its own observer or animation code. All effects animate transform and
 * opacity only (GPU-composited, zero layout shift).
 *
 * Behavior matrix:
 * - No JS / crawler:        content fully visible (CSS hides nothing without html.js).
 * - prefers-reduced-motion: decorative motion disabled; terminal renders statically.
 * - Touch devices:          tilt + magnetic + spotlight skipped; CSS :active tap states apply.
 *
 * Hooks (class / attribute → effect):
 *   .reveal, .reveal-start  → scroll-reveal (fade+rise / fade+slide-from-start, RTL-aware via CSS)
 *   [data-stagger]          → children's reveals staggered 90ms apart
 *   [data-reveal-text]      → headline reveals word-by-word when scrolled into view
 *   [data-counter]          → counts up to its number when scrolled into view
 *   [data-parallax]         → subtle scroll-linked translate (set speed via data-parallax-speed)
 *   .gradient-text          → animated gradient headline (CSS only)
 *   .js-terminal            → typewriter terminal, lines from its data-lines JSON
 *   .js-draw                → SVG stroke draws in when scrolled into view
 *   .js-tilt                → 3D hover tilt (+ .glow-border cursor glow via --mx/--my)
 *   .js-magnetic            → pulls slightly toward the cursor
 *   .js-spotlight           → section-level cursor glow via --mx/--my
 *   .js-marquee             → infinite scrolling strip (CSS only; duplicate content in markup)
 *   #scroll-progress        → top reading-progress bar (scaleX = scroll %)
 *   #nav-toggle / #mobile-menu → mobile navigation drawer
 */

const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
const finePointer = window.matchMedia('(pointer: fine)');
const STAGGER_MS = 90;
const WORD_STAGGER_MS = 55;

export function initAnimations() {
    initMobileNav();
    initScrollProgress(); // reading indicator — tracks user scroll, safe under reduced-motion

    if (reducedMotion.matches) {
        revealInstantly();
        return;
    }

    initScrollReveal();
    initRevealText();
    initCounters();
    initTerminals();
    initDraw();
    initParallax();

    if (finePointer.matches) {
        initTilt();
        initMagnetic();
        initSpotlight();
    }
}

/* ── Mobile navigation ─────────────────────────────────────── */

function initMobileNav() {
    const toggle = document.getElementById('nav-toggle');
    const menu = document.getElementById('mobile-menu');
    if (!toggle || !menu) return;

    toggle.addEventListener('click', () => {
        const open = menu.classList.toggle('hidden') === false;
        toggle.setAttribute('aria-expanded', String(open));
    });
}

/* ── Reading-progress bar ──────────────────────────────────── */

function initScrollProgress() {
    const bar = document.getElementById('scroll-progress');
    if (!bar) return;

    let ticking = false;
    const update = () => {
        const doc = document.documentElement;
        const max = doc.scrollHeight - doc.clientHeight;
        const progress = max > 0 ? doc.scrollTop / max : 0;
        bar.style.transform = `scaleX(${progress})`;
        ticking = false;
    };
    const onScroll = () => {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(update);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll, { passive: true });
    update();
}

/* ── Scroll reveal ─────────────────────────────────────────── */

function revealInstantly() {
    document.querySelectorAll('.reveal, .reveal-start').forEach((el) => el.classList.add('in'));
}

function applyStagger() {
    document.querySelectorAll('[data-stagger]').forEach((group) => {
        group.querySelectorAll('.reveal, .reveal-start').forEach((el, i) => {
            el.style.setProperty('--d', `${i * STAGGER_MS}ms`);
        });
    });
}

function initScrollReveal() {
    applyStagger();
    const observer = new IntersectionObserver(
        (entries, obs) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('in');
                obs.unobserve(entry.target);
            });
        },
        { threshold: 0.15, rootMargin: '0px 0px -40px 0px' },
    );
    document.querySelectorAll('.reveal, .reveal-start').forEach((el) => observer.observe(el));
}

/* ── Word-by-word headline reveal ──────────────────────────── */

function initRevealText() {
    const targets = document.querySelectorAll('[data-reveal-text]');
    if (!targets.length) return;

    targets.forEach(splitIntoWords);

    const observer = new IntersectionObserver(
        (entries, obs) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('in');
                obs.unobserve(entry.target);
            });
        },
        { threshold: 0.25 },
    );
    targets.forEach((el) => observer.observe(el));
}

function splitIntoWords(el) {
    const parts = el.textContent.split(/(\s+)/); // keep the whitespace tokens
    el.textContent = '';
    el.classList.add('reveal-text');

    let index = 0;
    for (const part of parts) {
        if (part.trim() === '') {
            el.append(part); // preserve spacing between words
            continue;
        }
        const word = document.createElement('span');
        word.className = 'reveal-word';
        word.textContent = part;
        word.style.setProperty('--d', `${index * WORD_STAGGER_MS}ms`);
        el.append(word);
        index += 1;
    }
}

/* ── Animated counters ─────────────────────────────────────── */

function initCounters() {
    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            animateCounter(entry.target);
            obs.unobserve(entry.target);
        });
    }, { threshold: 0.6 });
    document.querySelectorAll('[data-counter]').forEach((el) => observer.observe(el));
}

function animateCounter(el) {
    const target = Number(el.dataset.counter);
    const duration = 1400;
    const start = performance.now();

    const tick = (now) => {
        const progress = Math.min((now - start) / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        el.textContent = formatNumber(Math.round(target * eased));
        if (progress < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
}

function formatNumber(value) {
    return new Intl.NumberFormat(document.documentElement.lang).format(value);
}

/* ── Typewriter terminal ───────────────────────────────────── */

function initTerminals() {
    document.querySelectorAll('.js-terminal').forEach((el) => {
        const lines = JSON.parse(el.dataset.lines || '[]');
        if (lines.length) runTerminal(el, lines);
    });
}

async function runTerminal(el, lines) {
    /* eslint-disable no-constant-condition */
    while (true) {
        el.textContent = '';
        const cursor = createCursor();
        for (const line of lines) await typeLine(el, line, cursor);
        await wait(7000);
    }
}

function createCursor() {
    const cursor = document.createElement('span');
    cursor.className = 'terminal-cursor';
    cursor.setAttribute('aria-hidden', 'true');
    return cursor;
}

async function typeLine(el, line, cursor) {
    const row = document.createElement('div');
    row.className = `terminal-line-${line.type}`;
    if (line.type === 'blank') row.innerHTML = '&nbsp;';
    el.append(row);
    row.append(cursor);

    for (const char of line.text) {
        cursor.insertAdjacentText('beforebegin', char);
        await wait(line.type === 'code' ? 32 : 14);
    }
    await wait(line.type === 'blank' ? 60 : 360);
}

function wait(ms) {
    return new Promise((resolve) => setTimeout(resolve, ms));
}

/* ── SVG draw-in ───────────────────────────────────────────── */

const DRAW_SELECTOR = 'path, line, circle, rect, polyline, ellipse';

function initDraw() {
    const els = document.querySelectorAll('.js-draw');
    if (!els.length) return;

    els.forEach((el) => drawShapes(el).forEach(primeShape));

    const observer = new IntersectionObserver(
        (entries, obs) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                drawShapes(entry.target).forEach((shape) => {
                    shape.style.strokeDashoffset = '0';
                });
                obs.unobserve(entry.target);
            });
        },
        { threshold: 0.3 },
    );
    els.forEach((el) => observer.observe(el));
}

function drawShapes(el) {
    return el.matches(DRAW_SELECTOR) ? [el] : [...el.querySelectorAll(DRAW_SELECTOR)];
}

function primeShape(shape) {
    if (typeof shape.getTotalLength !== 'function') return;
    const length = shape.getTotalLength();
    if (!length) return;
    shape.style.strokeDasharray = String(length);
    shape.style.strokeDashoffset = String(length);
    shape.style.transition = 'stroke-dashoffset 1.2s cubic-bezier(0.22, 1, 0.36, 1)';
}

/* ── Scroll-linked parallax ────────────────────────────────── */

function initParallax() {
    const els = [...document.querySelectorAll('[data-parallax]')];
    if (!els.length) return;

    let ticking = false;
    const update = () => {
        const viewportCenter = window.innerHeight / 2;
        els.forEach((el) => {
            const rect = el.getBoundingClientRect();
            const speed = Number(el.dataset.parallaxSpeed || 0.12);
            const fromCenter = rect.top + rect.height / 2 - viewportCenter;
            el.style.transform = `translate3d(0, ${(-fromCenter * speed).toFixed(1)}px, 0)`;
        });
        ticking = false;
    };
    const onScroll = () => {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(update);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll, { passive: true });
    update();
}

/* ── 3D tilt + glow border ─────────────────────────────────── */

function initTilt() {
    document.querySelectorAll('.js-tilt').forEach((card) => {
        card.addEventListener('pointermove', (event) => applyTilt(card, event));
        card.addEventListener('pointerleave', () => resetTilt(card));
    });
}

function applyTilt(card, event) {
    const rect = card.getBoundingClientRect();
    const x = (event.clientX - rect.left) / rect.width;
    const y = (event.clientY - rect.top) / rect.height;

    card.style.setProperty('--mx', `${x * 100}%`);
    card.style.setProperty('--my', `${y * 100}%`);
    card.style.transform =
        `perspective(900px) rotateX(${(0.5 - y) * 8}deg) rotateY(${(x - 0.5) * 8}deg)`;
}

function resetTilt(card) {
    card.style.transform = '';
}

/* ── Magnetic CTA ──────────────────────────────────────────── */

function initMagnetic() {
    document.querySelectorAll('.js-magnetic').forEach((el) => {
        el.addEventListener('pointermove', (event) => applyMagnet(el, event));
        el.addEventListener('pointerleave', () => (el.style.transform = ''));
    });
}

function applyMagnet(el, event) {
    const rect = el.getBoundingClientRect();
    const pullX = (event.clientX - rect.left - rect.width / 2) * 0.22;
    const pullY = (event.clientY - rect.top - rect.height / 2) * 0.22;
    el.style.transform = `translate3d(${pullX}px, ${pullY}px, 0)`;
}

/* ── Section spotlight ─────────────────────────────────────── */

function initSpotlight() {
    document.querySelectorAll('.js-spotlight').forEach((el) => {
        el.addEventListener('pointermove', (event) => {
            const rect = el.getBoundingClientRect();
            el.style.setProperty('--mx', `${event.clientX - rect.left}px`);
            el.style.setProperty('--my', `${event.clientY - rect.top}px`);
        });
    });
}
