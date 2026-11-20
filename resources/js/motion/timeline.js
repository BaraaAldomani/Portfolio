/**
 * timeline.js — turns the scroll position into a place on a section timeline.
 *
 * Every [data-j] section is one unit. A plain section ends when its bottom
 * reaches the top of the viewport. A section whose caption is pinned (a
 * station, or anything marked data-j-pin) ends when its pin lets go, one
 * screen earlier, and the section after it starts right there.
 *
 * Each section also gets --jp (how far through it we are, 0 → 1), which the
 * CSS uses to bring its caption in and out.
 */
const clamp = (v, a, b) => Math.min(b, Math.max(a, v));

export function createTimeline(sections) {
    const bounds = () => {
        const vh = window.innerHeight;
        const list = [];
        sections.forEach((el, i) => {
            const top = el.getBoundingClientRect().top + window.scrollY;
            const pinned = el.dataset.station || el.hasAttribute('data-j-pin');
            const end = top + el.offsetHeight - (pinned ? vh : 0);
            const start = i ? list[i - 1].end : top;
            list.push({ start, end: Math.max(end, start + 1) });
        });
        return list;
    };

    return {
        /** u: position on the timeline. tail: 0 → 1 while the screen after the last section rises. */
        read() {
            const y = window.scrollY;
            const list = bounds();
            let u = sections.length;
            for (let i = 0; i < list.length; i++) {
                if (y < list[i].end) {
                    u = i + clamp((y - list[i].start) / (list[i].end - list[i].start), 0, 1);
                    break;
                }
            }
            sections.forEach((el, i) => el.style.setProperty('--jp', clamp(u - i, 0, 1).toFixed(3)));
            const vh = window.innerHeight;
            return { u, tail: clamp((y - (list[list.length - 1].end - vh)) / vh, 0, 1) };
        },

        /** Scroll offset that corresponds to a point on the timeline. */
        yFor(u) {
            const list = bounds();
            const i = Math.min(list.length - 1, Math.floor(u));
            return list[i].start + (u - i) * (list[i].end - list[i].start);
        },
    };
}
