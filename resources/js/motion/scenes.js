/**
 * scenes.js — everything that moves with the scroll.
 *
 *   [data-marquee]    a running line of type, pushed along by scroll speed
 *   [data-drift]      an image that drifts inside its frame (parallax)
 */
export function initScenes({ gsap, reduced }) {
    // The WhatsApp button appears once the hero is behind us.
    const fab = document.querySelector('[data-wa-fab]');
    if (fab) {
        const hasHero = Boolean(document.querySelector('.intro'));
        const closing = document.querySelector('.closing');
        const show = () => {
            const past = !hasHero || window.scrollY > window.innerHeight * 0.6;
            const atEnd = closing && closing.getBoundingClientRect().top < window.innerHeight * 0.55;
            fab.classList.toggle('is-on', past && !atEnd);
        };
        window.addEventListener('scroll', show, { passive: true });
        show();
    }

    initMarquees({ gsap, reduced });
    if (reduced) return;

    document.querySelectorAll('[data-drift]').forEach((el) => {
        gsap.fromTo(el, { yPercent: 0 }, {
            yPercent: Number(el.dataset.drift || -10),
            ease: 'none',
            scrollTrigger: { trigger: el.parentElement, start: 'top bottom', end: 'bottom top', scrub: true },
        });
    });
}

/**
 * Marquees: the track is cloned until it is wider than two viewports, then
 * moved a little every frame plus a kick from the scroll speed (and in the
 * direction of the scroll).
 */
function initMarquees({ gsap, reduced }) {
    document.querySelectorAll('[data-marquee]').forEach((el) => {
        const track = el.querySelector('.mq__track');
        if (!track) return;
        const items = [...track.children];
        const unit = () => items.reduce((sum, item) => sum + item.getBoundingClientRect().width, 0);

        let width = unit();
        if (!width) return;
        const copies = Math.max(2, Math.ceil((window.innerWidth * 2.2) / width));
        for (let i = 1; i < copies; i++) items.forEach((item) => track.appendChild(item.cloneNode(true)));
        if (reduced) return;

        const dir = Number(el.dataset.marqueeDir || 1);
        let x = 0;
        let lastY = window.scrollY;
        let kick = 0;
        let visible = false;
        new IntersectionObserver(([entry]) => { visible = entry.isIntersecting; }).observe(el);
        window.addEventListener('resize', () => { width = unit() || width; }, { passive: true });

        gsap.ticker.add((_, delta) => {
            if (!visible) return;
            const y = window.scrollY;
            const dy = y - lastY;
            lastY = y;
            kick += (dy * 0.9 - kick) * 0.12;
            x -= (delta * 0.055 + Math.abs(kick)) * dir * (kick < -0.5 ? -1 : 1);
            x = ((x % width) + width) % width;
            track.style.transform = `translate3d(${-x}px,0,0)`;
        });
    });
}
