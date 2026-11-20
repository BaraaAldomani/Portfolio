import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import Lenis from 'lenis';

import { initTracking } from './track';
import { initReveals, initCounters, initScrub } from './motion/reveal';
import { initScenes } from './motion/scenes';
import { runLoader } from './ui/loader';
import { initNav } from './ui/nav';
import { initCursor } from './ui/cursor';
import { initMagnetic } from './ui/magnetic';
import { initClock } from './ui/clock';
import { initRetheme } from './ui/retheme';

// The inline <head> script undoes html.js / html.is-loading if this flag
// never appears.
window.__booted = true;

gsap.registerPlugin(ScrollTrigger);

const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
const env = { reduced, finePointer, gsap, ScrollTrigger };

/**
 * The home page is a drawing with a camera that travels it (motion/board.js).
 * It is its own chunk, so it never blocks first paint and other pages never
 * download it. If it cannot run, the page stays readable as plain HTML.
 */
async function mountBoard() {
    const root = document.querySelector('[data-board]');
    if (!root) return null;
    try {
        const board = (await import('./motion/board')).mountBoard(root, env);
        if (!board) root.classList.add('no-webgl');
        return board;
    } catch {
        root.classList.add('no-webgl');
        return null;
    }
}

document.addEventListener('DOMContentLoaded', async () => {
    initTracking();
    initNav();
    initClock();

    // Headlines are split now (hidden under the loader) and released when the
    // curtain lifts.
    const release = initReveals(env);
    const board = mountBoard();

    // Inertial scrolling on desktop only; touch devices keep native scroll.
    if (!reduced && finePointer) {
        const lenis = new Lenis({ duration: 1.15, anchors: true });
        lenis.on('scroll', ScrollTrigger.update);
        gsap.ticker.add((time) => lenis.raf(time * 1000));
        gsap.ticker.lagSmoothing(0);
        initCursor(env);
    }

    initScrub(env);
    initScenes(env);
    initRetheme();
    initMagnetic(env);

    if (document.body.dataset.page === 'services') {
        const { default: services } = await import('./pages/services');
        services(env);
    }

    ScrollTrigger.refresh();

    await runLoader(env);
    release();
    initCounters(env);
    (await board)?.start();
});
