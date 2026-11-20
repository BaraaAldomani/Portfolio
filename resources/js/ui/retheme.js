/**
 * retheme.js — the consultant's site re-colours itself from its dashboard.
 * Here the visitor gets to try it: each [data-tint] swatch re-tints the whole
 * world (the page accent) and hue-shifts the phone screens.
 */
export function initRetheme() {
    document.querySelectorAll('[data-retheme]').forEach((scope) => {
        const swatches = [...scope.querySelectorAll('[data-tint]')];
        swatches.forEach((swatch) => {
            swatch.addEventListener('click', () => {
                document.documentElement.style.setProperty('--tint', swatch.dataset.tint);
                scope.style.setProperty('--hue', `${swatch.dataset.hue || 0}deg`);
                swatches.forEach((s) => s.setAttribute('aria-pressed', String(s === swatch)));
            });
        });
    });
}
