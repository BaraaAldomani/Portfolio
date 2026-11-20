/**
 * cursor.js — a cyan dot that trails the pointer (fine pointers only).
 * Over [data-cursor="label"] it swells into a disc carrying that label
 * ("افتح", "اسحب"); over ordinary links it becomes a soft halo.
 * The native cursor stays visible: this is an accent, not a replacement.
 */
export function initCursor({ gsap }) {
    const el = document.createElement('div');
    el.className = 'cursor';
    el.setAttribute('aria-hidden', 'true');
    el.innerHTML = '<span></span>';
    document.body.appendChild(el);
    const label = el.firstElementChild;

    const xTo = gsap.quickTo(el, 'x', { duration: 0.35, ease: 'power3.out' });
    const yTo = gsap.quickTo(el, 'y', { duration: 0.35, ease: 'power3.out' });

    window.addEventListener('pointermove', (e) => {
        if (e.pointerType !== 'mouse') return;
        el.classList.add('is-on');
        xTo(e.clientX);
        yTo(e.clientY);

        const labelled = e.target.closest?.('[data-cursor]');
        const link = !labelled && e.target.closest?.('a, button, summary, input, textarea, select, label');
        el.classList.toggle('has-label', Boolean(labelled));
        el.classList.toggle('is-link', Boolean(link));
        if (labelled) label.textContent = labelled.dataset.cursor;
    }, { passive: true });

    document.addEventListener('mouseleave', () => el.classList.remove('is-on'));
}
