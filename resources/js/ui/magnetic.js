/**
 * magnetic.js — round buttons lean toward the pointer and snap back with a
 * little spring. Fine pointers only.
 */
export function initMagnetic({ gsap, finePointer, reduced }) {
    if (!finePointer || reduced) return;

    document.querySelectorAll('[data-magnetic]').forEach((el) => {
        const inner = el.firstElementChild;
        const move = (target, k) => ({
            x: gsap.quickTo(target, 'x', { duration: 0.8, ease: 'elastic.out(1, 0.45)' }),
            y: gsap.quickTo(target, 'y', { duration: 0.8, ease: 'elastic.out(1, 0.45)' }),
            k,
        });
        const outer = move(el, 0.32);
        const label = inner ? move(inner, 0.14) : null;

        el.addEventListener('pointermove', (e) => {
            const r = el.getBoundingClientRect();
            const dx = e.clientX - (r.left + r.width / 2);
            const dy = e.clientY - (r.top + r.height / 2);
            outer.x(dx * outer.k);
            outer.y(dy * outer.k);
            label?.x(dx * label.k);
            label?.y(dy * label.k);
        });
        el.addEventListener('pointerleave', () => {
            outer.x(0);
            outer.y(0);
            label?.x(0);
            label?.y(0);
        });
    });
}
