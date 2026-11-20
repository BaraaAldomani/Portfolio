/**
 * nav.js — the fixed header: hides while scrolling down, returns on the way
 * up, gains a frosted backdrop once the page has moved, and opens the
 * full-screen menu from the position of the menu button (a growing dot).
 */
export function initNav() {
    const nav = document.querySelector('.nav');
    const menu = document.getElementById('menu');
    const toggle = document.getElementById('menu-toggle');
    if (!nav) return;

    let last = window.scrollY;
    const onScroll = () => {
        const y = window.scrollY;
        nav.classList.toggle('is-solid', y > 24);
        const open = document.documentElement.classList.contains('menu-open');
        if (!open) nav.classList.toggle('is-hidden', y > last && y > 320);
        last = y;
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    if (!menu || !toggle) return;

    const setOpen = (open) => {
        const rect = toggle.getBoundingClientRect();
        menu.style.setProperty('--mx', `${rect.left + rect.width / 2}px`);
        menu.classList.toggle('is-open', open);
        document.documentElement.classList.toggle('menu-open', open);
        toggle.setAttribute('aria-expanded', String(open));
        menu.toggleAttribute('inert', !open);
    };

    toggle.addEventListener('click', () => setOpen(!menu.classList.contains('is-open')));
    menu.addEventListener('click', (e) => {
        if (e.target.closest('a')) setOpen(false);
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && menu.classList.contains('is-open')) {
            setOpen(false);
            toggle.focus();
        }
    });
}
