/**
 * services.js — the project calculator. Prices come from config/pricing.php
 * (embedded as JSON); this only adds them up, animates the numbers and hands
 * the result to WhatsApp as a ready-to-send message.
 */
export default function services({ gsap, reduced }) {
    const form = document.querySelector('[data-estimator]');
    if (!form) return;

    const cfg = JSON.parse(form.dataset.config);
    const out = Object.fromEntries(['low', 'high', 'weeks'].map((k) => [k, form.querySelector(`[data-est="${k}"]`)]));
    const fmt = new Intl.NumberFormat('en-US');
    const round = (n, step = 500) => Math.round(n / step) * step;
    const shown = { low: 0, high: 0 };
    let current = null;

    const read = () => {
        const data = new FormData(form);
        return { type: data.get('type'), extras: data.getAll('extras'), pace: data.get('pace') };
    };

    const compute = ({ type, extras, pace }) => {
        const offer = cfg.offers[type];
        const add = extras.reduce((sum, key) => sum + cfg.extras[key].add, 0);
        const extraWeeks = extras.reduce((sum, key) => sum + cfg.extras[key].weeks, 0);
        const low = round((offer.from + add) * cfg.pace[pace].price);
        const weeks = offer.weeks.map((w) => Math.max(1, Math.round((w + extraWeeks) * cfg.pace[pace].time)));
        return { low, high: round(low * cfg.spread), weeks };
    };

    const render = (first = false) => {
        const choice = read();
        current = { choice, ...compute(choice) };
        out.weeks.textContent = `${current.weeks[0]} ${cfg.labels.to} ${current.weeks[1]}`;

        if (first || reduced) {
            shown.low = current.low;
            shown.high = current.high;
            out.low.textContent = fmt.format(current.low);
            out.high.textContent = fmt.format(current.high);
            return;
        }
        gsap.to(shown, {
            low: current.low,
            high: current.high,
            duration: 0.7,
            ease: 'power3.out',
            onUpdate: () => {
                out.low.textContent = fmt.format(round(shown.low, 100));
                out.high.textContent = fmt.format(round(shown.high, 100));
            },
        });
    };

    form.addEventListener('change', () => render());
    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const { choice, low, high, weeks } = current;
        const L = cfg.labels;
        const extras = choice.extras.length ? choice.extras.map((k) => L.extras[k]).join('، ') : L.none;
        const text = [
            L.intro,
            `${L.type}: ${L.offers[choice.type]}`,
            `${L.extrasLabel}: ${extras}`,
            `${L.paceLabel}: ${L.pace[choice.pace]}`,
            `${L.range}: ${fmt.format(low)} ${L.to} ${fmt.format(high)} ${L.currency} (${weeks[0]} ${L.to} ${weeks[1]} ${L.weeks})`,
        ].join('\n');
        window.open(`${cfg.whatsapp}?text=${encodeURIComponent(text)}`, '_blank', 'noopener');
    });

    // Arriving from a service link (/services#system) preselects that type.
    const preset = location.hash.slice(1);
    if (cfg.offers[preset]) form.querySelector(`input[name="type"][value="${preset}"]`).checked = true;

    render(true);
}
