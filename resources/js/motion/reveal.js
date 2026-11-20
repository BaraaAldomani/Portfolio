/**
 * reveal.js — turns on the CSS motion primitives when things scroll into view.
 * The animations themselves are CSS (see app.css); JS only adds `.in`.
 *
 *   [data-reveal]      fade + rise            (optional --d delay)
 *   [data-words]       headline, word-by-word mask reveal
 *   [data-stagger]     children with the hooks above get stepped delays
 *   [data-scrub]       paragraph whose words come into focus with the scroll
 *   [data-counter]     counts up to its number (the real number ships in HTML)
 */

const WORD_STEP = 0.07;
const STAGGER_STEP = 0.09;

/** Splits now, and returns a function that starts the observing. */
export function initReveals({ reduced }) {
    document.querySelectorAll('[data-words]').forEach((el) => splitWords(el, 'w', true));

    document.querySelectorAll('[data-stagger]').forEach((group) => {
        group.querySelectorAll('[data-reveal]').forEach((el, i) => {
            el.style.setProperty('--d', `${(i * STAGGER_STEP).toFixed(2)}s`);
        });
    });

    const targets = document.querySelectorAll('[data-reveal], [data-words]');

    return () => {
        if (reduced || !('IntersectionObserver' in window)) {
            targets.forEach((el) => el.classList.add('in'));
            return;
        }
        const io = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) return;
                    entry.target.classList.add('in');
                    io.unobserve(entry.target);
                });
            },
            { threshold: 0.1, rootMargin: '0px 0px -6% 0px' },
        );
        targets.forEach((el) => io.observe(el));
    };
}

/**
 * Wrap each WORD (never letters: Arabic is cursive) in a span. With `mask`,
 * each word gets a clipping parent so it can slide up into place. Inline
 * children such as <em class="alt"> are kept and their words wrapped in place.
 */
function splitWords(root, className, mask) {
    let index = 0;
    const walk = (node) => {
        [...node.childNodes].forEach((child) => {
            if (child.nodeType === Node.TEXT_NODE) {
                const frag = document.createDocumentFragment();
                child.textContent.split(/(\s+)/).forEach((part) => {
                    if (!part) return;
                    if (/^\s+$/.test(part)) {
                        frag.appendChild(document.createTextNode(' '));
                        return;
                    }
                    const word = document.createElement('span');
                    word.className = className;
                    if (mask) {
                        const inner = document.createElement('span');
                        inner.textContent = part;
                        inner.style.setProperty('--wd', `${(index++ * WORD_STEP).toFixed(3)}s`);
                        word.appendChild(inner);
                    } else {
                        word.textContent = part;
                    }
                    frag.appendChild(word);
                });
                child.replaceWith(frag);
            } else if (child.nodeType === Node.ELEMENT_NODE) {
                walk(child);
            }
        });
    };
    walk(root);
    root.classList.add('is-split');
}

export function initScrub({ gsap, reduced }) {
    if (reduced) return;
    document.querySelectorAll('[data-scrub]').forEach((el) => {
        splitWords(el, 'sw', false);
        gsap.to(el.querySelectorAll('.sw'), {
            opacity: 1,
            ease: 'none',
            stagger: 0.12,
            scrollTrigger: { trigger: el, start: 'top 82%', end: 'bottom 52%', scrub: 0.4 },
        });
    });
}

export function initCounters({ reduced }) {
    const counters = document.querySelectorAll('[data-counter]');
    if (reduced || !counters.length) return;

    const format = new Intl.NumberFormat(`${document.documentElement.lang}-u-nu-latn`);
    counters.forEach((el) => { el.textContent = format.format(0); });

    const io = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                io.unobserve(entry.target);
                const el = entry.target;
                const target = Number(el.dataset.counter);
                const start = performance.now();
                const tick = (now) => {
                    const t = Math.min((now - start) / 1600, 1);
                    el.textContent = format.format(Math.round(target * (1 - Math.pow(1 - t, 4))));
                    if (t < 1) requestAnimationFrame(tick);
                };
                requestAnimationFrame(tick);
            });
        },
        { threshold: 0.6 },
    );
    counters.forEach((el) => io.observe(el));
}
