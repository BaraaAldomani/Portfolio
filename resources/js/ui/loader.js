/**
 * loader.js — the first view of a session opens on a black curtain: the mark
 * draws itself, a counter runs to 100, the curtain lifts. Later pages in the
 * same session skip it (the head script only adds html.is-loading once).
 * Resolves the moment the curtain starts to lift, so the hero can animate in
 * underneath it.
 */
export function runLoader({ gsap, reduced }) {
    const root = document.documentElement;
    const el = document.querySelector('.loader');
    if (!el || !root.classList.contains('is-loading')) return Promise.resolve();

    try {
        sessionStorage.setItem('seen', '1');
    } catch { /* private mode: the loader simply shows again */ }

    const count = el.querySelector('[data-loader-count]');
    const fonts = document.fonts?.ready ?? Promise.resolve();
    const state = { n: 0 };

    return new Promise((resolve) => {
        gsap.to(state, {
            n: 100,
            duration: reduced ? 0.01 : 1.45,
            ease: 'power2.inOut',
            onUpdate: () => { if (count) count.textContent = String(Math.round(state.n)); },
            onComplete: async () => {
                await Promise.race([fonts, new Promise((r) => setTimeout(r, 800))]);
                el.classList.add('is-done');
                resolve();
                setTimeout(() => root.classList.remove('is-loading'), reduced ? 0 : 1050);
            },
        });
    });
}
