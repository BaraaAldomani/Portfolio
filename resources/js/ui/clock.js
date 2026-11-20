/** clock.js — [data-clock] shows the current time in Riyadh. */
export function initClock() {
    const els = document.querySelectorAll('[data-clock]');
    if (!els.length) return;
    const fmt = new Intl.DateTimeFormat('en-GB', { hour: '2-digit', minute: '2-digit', timeZone: 'Asia/Riyadh' });
    const tick = () => {
        const now = fmt.format(new Date());
        els.forEach((el) => { el.textContent = now; });
    };
    tick();
    setInterval(tick, 20000);
}
