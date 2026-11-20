/**
 * track.js — sends conversion events to whichever analytics the dashboard
 * enabled (Umami and/or GA4). Does nothing when neither is loaded.
 *
 *   [data-track="whatsapp"]        → "whatsapp_click" on click
 *   [data-track-from="footer"]     → added as the "from" property
 *   [data-track-view="name"]       → "name" once, when the page loads
 *                                    (e.g. the contact-form success notice)
 */

function send(name, data = {}) {
    const payload = { ...data, page: window.location.pathname };
    try {
        window.umami?.track?.(name, payload);
        if (typeof window.gtag === 'function') window.gtag('event', name, payload);
    } catch {
        // Analytics must never break the page.
    }
}

export function initTracking() {
    document.addEventListener('click', (event) => {
        const el = event.target.closest('[data-track]');
        if (!el) return;
        send(`${el.dataset.track}_click`, { from: el.dataset.trackFrom || 'page' });
    });

    document.querySelectorAll('[data-track-view]').forEach((el) => send(el.dataset.trackView));
}
