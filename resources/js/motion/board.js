/**
 * board.js — the home page as one flat drawing, and a camera that travels it.
 *
 * The drawing speaks the logo's language without copying the logo: one
 * weight of line, corners cut at 45°, octagons, dashed paths, ring nodes, a
 * filled dot, a solid arrowhead.
 *
 * The story is told by one line, in the order the camera meets it:
 *
 *   the hero      an idea is typed, becomes a diagram, becomes a real product (it loops)
 *   the idea      the line is a tangle, with the founder's questions around it
 *   the analysis  the same line, in order: three rows, each an answer
 *   the design    the line draws a screen; what goes in it is still dashed
 *   the build     the screen again, filled in piece by piece, then checked
 *   the launch    the line draws a rocket, and the rocket flies to the button
 *
 * A pen travels the line. Ahead of it the line is dashed (planned); behind
 * it, solid (built). The interface that gets built is invented: boxes, bars
 * and a chart, never a real site.
 *
 * The sheet is laid out for a right-to-left reader (the line travels to the
 * left) and mirrored for a left-to-right one.
 *
 * Canvas 2D only: no 3D library, text stays sharp at any zoom, light on
 * phones. Scroll is the timeline (timeline.js). Captions are ordinary HTML.
 */
import { createTimeline } from './timeline';

const TAU = Math.PI * 2;
const clamp = (v, a, b) => Math.min(b, Math.max(a, v));
const lerp = (a, b, t) => a + (b - a) * t;
const smooth = (t, a = 0, b = 1) => {
    const x = clamp((t - a) / (b - a), 0, 1);
    return x * x * (3 - 2 * x);
};

/**
 * A smooth curve through keyed values that never overshoots them (monotone
 * cubic, Fritsch-Carlson). The camera can glide through a key or rest on a
 * pair of equal ones, but it never swings past a framed shot.
 */
function curve(us, vs) {
    const n = us.length;
    const d = [];
    const m = new Array(n).fill(0);
    for (let i = 0; i < n - 1; i++) d.push((vs[i + 1] - vs[i]) / (us[i + 1] - us[i]));
    m[0] = d[0];
    m[n - 1] = d[n - 2];
    for (let i = 1; i < n - 1; i++) m[i] = d[i - 1] * d[i] <= 0 ? 0 : (d[i - 1] + d[i]) / 2;
    for (let i = 0; i < n - 1; i++) {
        if (d[i] === 0) {
            m[i] = m[i + 1] = 0;
            continue;
        }
        const a = m[i] / d[i];
        const b = m[i + 1] / d[i];
        const s = a * a + b * b;
        if (s > 9) {
            const k = 3 / Math.sqrt(s);
            m[i] = k * a * d[i];
            m[i + 1] = k * b * d[i];
        }
    }
    return (i, t) => {
        const h = us[i + 1] - us[i];
        const t2 = t * t;
        const t3 = t2 * t;
        return (2 * t3 - 3 * t2 + 1) * vs[i] + (t3 - 2 * t2 + t) * h * m[i] + (-2 * t3 + 3 * t2) * vs[i + 1] + (t3 - t2) * h * m[i + 1];
    };
}

// The hero scene: a prompt, a row of nodes, a small window, and the port the line leaves by.
const HERO = {
    prompt: { x: -450, y: -440, w: 900, h: 130 },
    nodeY: -150,
    win: { x: -320, y: 70, w: 640, h: 400, bar: 40 },
    port: { x: -430, y: 290 },
    centre: { x: 0, y: 20 },
    size: { w: 960, h: 1040 },
};
const FONT = {
    display: '"Alexandria Variable", "Readex Pro Variable", sans-serif',
    text: '"Readex Pro Variable", "Alexandria Variable", sans-serif',
    mono: '"JetBrains Mono Variable", ui-monospace, monospace',
};

// ── The drawings on the line ──
// Each is laid out round its own centre. The line comes in 750 to one side
// and leaves 750 to the other, then steps down at 45° to the next one.
const HALF = 750;
const STEP = 900;
const SCREEN = { x: -560, y: -400, w: 1120, h: 800, bar: 100, cut: 50 };
const PHONE = { x: -700, y: 110, w: 220, h: 430, cut: 30 };
// The rocket, as one closed stroke. It starts and ends at the middle of the
// nozzle, which is where the line comes in: the rocket sits centred on the
// end of the line, and the line is its axis.
const ROCKET = [[0, 340], [100, 340], [70, 270], [130, 270], [260, 400], [260, 260], [130, 130], [130, -290], [0, -420], [-130, -290], [-130, 130], [-260, 260], [-260, 400], [-130, 270], [-70, 270], [-100, 340], [0, 340]];
const GROUND = 560; // the line runs along here, then turns up into the nozzle
const WINDOW = { x: 0, y: -130, r: 58 };

/** Cuts every right-angled corner of a polyline at 45°. A third item in a point is its name; it is kept. */
function chamfer(points, cut) {
    const out = [points[0]];
    for (let i = 1; i < points.length - 1; i++) {
        const [ax, ay] = points[i - 1];
        const [bx, by] = points[i];
        const [cx, cy] = points[i + 1];
        const l1 = Math.hypot(bx - ax, by - ay);
        const l2 = Math.hypot(cx - bx, cy - by);
        const dot = ((bx - ax) * (cx - bx) + (by - ay) * (cy - by)) / (l1 * l2);
        if (Math.abs(dot) > 0.2) {
            out.push(points[i]);
            continue;
        }
        const k = Math.min(cut, l1 / 2, l2 / 2);
        out.push([bx - ((bx - ax) / l1) * k, by - ((by - ay) / l1) * k], [bx + ((cx - bx) / l2) * k, by + ((cy - by) / l2) * k]);
    }
    out.push(points[points.length - 1]);
    return out;
}

/** The idea: a scribble. A smooth line through points thrown across an ellipse, always the same ones. */
function tangle() {
    let seed = 97;
    const random = () => {
        seed = (seed * 1664525 + 1013904223) >>> 0;
        return seed / 4294967296;
    };
    const through = [[HALF, 0], [600, 0]];
    const count = 18;
    let angle = random() * TAU;
    for (let k = 0; k < count; k++) {
        // Each point lands well round from the last, so the line keeps crossing itself.
        angle += 2.2 + random() * 1.5;
        const reach = 0.55 + random() * 0.45;
        through.push([lerp(210, -210, k / (count - 1)) + Math.cos(angle) * 440 * reach, Math.sin(angle) * 420 * reach]);
    }
    through.push([-600, 0], [-HALF, 0]);

    const out = [];
    const last = through.length - 1;
    for (let i = 0; i < last; i++) {
        const p0 = through[Math.max(0, i - 1)];
        const p1 = through[i];
        const p2 = through[i + 1];
        const p3 = through[Math.min(last, i + 2)];
        for (let n = 0; n < 16; n++) {
            const t = n / 16;
            const t2 = t * t;
            const t3 = t2 * t;
            out.push([0, 1].map((d) => 0.5 * (2 * p1[d] + (p2[d] - p0[d]) * t + (2 * p0[d] - 5 * p1[d] + 4 * p2[d] - p3[d]) * t2 + (3 * p1[d] - p0[d] - 3 * p2[d] + p3[d]) * t3)));
        }
    }
    out.push(through[last]);
    return out;
}

/** The analysis: the same line in three tidy rows. r0, r1, r2 mark the middle of each. */
const ROWS = [[40, -260], [-70, 0], [-35, 260]];
function rows() {
    return chamfer([
        [HALF, 0], [640, 0], [640, -260], [...ROWS[0], 'r0'], [-560, -260],
        [-560, 0], [...ROWS[1], 'r1'], [420, 0],
        [420, 260], [...ROWS[2], 'r2'], [-490, 260], [-HALF, 0],
    ], 44);
}

/**
 * The design and the build: a screen drawn in one stroke. In along the line
 * under the bar, over the top, back along the bar, round the bottom, out.
 */
function screen() {
    const { x, y, w, h, bar, cut } = SCREEN;
    const r = x + w;
    const b = y + h;
    const m = y + bar;
    return [
        [HALF, m], [r, m],
        [r, y + cut], [r - cut, y], [x + cut, y], [x, y + cut], [x, m],
        [r, m],
        [r, b - cut], [r - cut, b], [x + cut, b], [x, b - cut], [x, m],
        [-HALF, m],
    ];
}

// What stands on the line, in order. `done` is how far through its stop the
// pen has finished the drawing's own line; `box` is what the camera frames.
const FIGS = [
    { kind: 'idea', path: tangle, done: 0.86, box: { x: 0, y: 0, w: 1320, h: 1200 } },
    { kind: 'analysis', path: rows, done: 0.84, box: { x: 0, y: -30, w: 1320, h: 820 } },
    { kind: 'design', path: screen, done: 0.6, box: { x: -70, y: 70, w: 1320, h: 980 } },
    { kind: 'build', path: screen, done: 0.5, box: { x: -70, y: 70, w: 1320, h: 980 } },
    { kind: 'launch', path: () => [[HALF, GROUND], [90, GROUND], [0, GROUND - 90], ROCKET[0]], done: 0.4, box: { x: 40, y: 70, w: 1150, h: 1140 } },
];
// The launch stop, and the landing after it, as parts of the launch stop's own length.
const FLY = { trace: [0.4, 0.64], hop: [0.64, 0.7], fire: [0.72, 0.84], from: 0.84, to: 1.56 };

const pointAt = (path, s) => {
    for (let i = 1; i < path.length; i++) {
        if (path[i].s >= s) {
            const a = path[i - 1];
            const b = path[i];
            const t = (s - a.s) / (b.s - a.s || 1);
            return { x: lerp(a.x, b.x, t), y: lerp(a.y, b.y, t) };
        }
    }
    return path[path.length - 1];
};
const measured = (points) => {
    let len = 0;
    return points.map(([x, y], i) => {
        if (i) len += Math.hypot(x - points[i - 1][0], y - points[i - 1][1]);
        return { x, y, s: len };
    });
};

/** Lays out the sheet: the pen's line, and the drawings it becomes. `mir` is 1, or -1 to mirror the sheet. */
function buildWorld(cfg, mir) {
    const pts = [];
    const marks = {};
    let len = 0;
    const to = (x, y) => {
        const wx = mir * x;
        if (pts.length) {
            const p = pts[pts.length - 1];
            len += Math.hypot(wx - p.x, y - p.y);
        }
        pts.push({ x: wx, y, s: len });
    };
    const mark = (name) => { marks[name] = len; };

    // Out of the hero, down, and a 45° step to where the first drawing begins.
    to(HERO.port.x, HERO.port.y);
    to(-1200, HERO.port.y);
    to(-1500, HERO.port.y + 300);
    to(-1500, 950);
    let ex = -2150;
    let ey = 1400;
    to(ex + 200, ey);

    const figs = FIGS.map((fig, j) => {
        const path = fig.path();
        const cx = ex - HALF;
        const cy = ey - path[0][1];
        to(ex, ey);
        mark(`f${j}in`);
        path.slice(1).forEach(([lx, ly, name]) => {
            to(cx + lx, cy + ly);
            if (name) mark(`f${j}${name}`);
        });
        mark(`f${j}out`);
        if (j < FIGS.length - 1) {
            const [lx, ly] = path[path.length - 1];
            to(cx + lx - 200, cy + ly);
            ex = cx + lx - 400 - STEP;
            ey = cy + ly + STEP;
            to(ex + 200, ey);
        }
        return { ...fig, j, x: mir * cx, y: cy, title: (cfg.steps || [])[j], box: { x: mir * (cx + fig.box.x), y: cy + fig.box.y, w: fig.box.w, h: fig.box.h }, station: null };
    });
    mark('final');

    return { pts, marks, figs, length: len };
}

/** The shot list. Camera positions depend on the viewport, so it is rebuilt on resize. */
function buildKeys(world, landingAt, contentCount, view, rtl) {
    const { W, H, small } = view;
    const { marks, figs } = world;
    const side = rtl ? 1 : -1; // which way the picture is pushed to make room for the text
    const keys = [];
    const add = (u, x, y, zoom, o = {}) => keys.push({ u, x, y, lz: Math.log(zoom), rot: o.rot || 0, head: o.head ?? 0, scrim: o.scrim || 0, shade: o.shade ?? 0 });

    // Hero: the scene beside the headline (under it on a phone).
    const z0 = small ? Math.min((0.88 * W) / HERO.size.w, (0.36 * H) / HERO.size.h) : Math.min((0.5 * W) / HERO.size.w, (0.8 * H) / HERO.size.h);
    const hx = HERO.centre.x + (small ? 0 : (side * 0.22 * W) / z0);
    const hy = HERO.centre.y - (small ? (0.27 * H) / z0 : (0.04 * H) / z0);
    add(0, hx, hy, z0);
    add(0.8, hx, hy, z0);

    // Where a drawing sits on screen while its card is read: beside the card,
    // or above it on a phone.
    const frame = (fig) => {
        const { x, y, w, h } = fig.box;
        if (small) {
            const z = Math.min((0.94 * W) / w, (0.38 * H) / h);
            return { x, y: y + (0.15 * H) / z, z };
        }
        const z = Math.min((0.5 * W) / w, (0.64 * H) / h);
        return { x: x + (side * 0.2 * W) / z, y: y + (0.02 * H) / z, z };
    };

    let end = { u: 1, x: hx, y: hy, z: z0 };
    let pad = null; // the camera at lift-off: the rocket's flight is laid out from it
    figs.forEach((fig, j) => {
        if (!fig.station) return;
        const i = fig.station.index;
        const fr = frame(fig);
        const inAt = marks[`f${j}in`];
        const outAt = marks[`f${j}out`];
        if (j) {
            // On the way: far enough back to see where the line came from.
            const prev = figs[j - 1];
            const zm = Math.max(Math.min((0.86 * W) / 4400, (0.8 * H) / 2400), fr.z * 0.5);
            add(i, (prev.box.x + fig.box.x) / 2, (prev.box.y + fig.box.y) / 2, zm, { head: lerp(marks[`f${j - 1}out`], inAt, 0.5), rot: (j % 2 ? 0.03 : -0.03) * side });
        }
        add(i + 0.3, fr.x, fr.y, fr.z, { head: inAt, shade: 1 });
        add(i + fig.done, fr.x, fr.y, fr.z * 1.015, { head: outAt, shade: 1 });
        if (fig.done < 0.85) add(i + 0.88, fr.x, fr.y, fr.z * 1.03, { head: outAt, shade: 1 });
        if (j < figs.length - 1) add(i + 0.95, fr.x, fr.y, fr.z * 0.94, { head: outAt, shade: 0.4 });
        end = { u: i + 1, x: fr.x, y: fr.y, z: fr.z };
    });

    // The landing: the rocket crosses the screen to the button while the pad
    // slides up with the page, so the words arrive on a clean sheet.
    if (landingAt >= 0) {
        pad = { x: end.x, y: end.y, z: end.z * 1.03 };
        add(end.u, pad.x, pad.y, pad.z, { head: marks.final });
        end.y += (0.95 * H) / end.z;
        add(landingAt + 0.5, end.x, end.y, end.z, { head: marks.final });
        end.u = landingAt + 1;
    }
    add(end.u, end.x, end.y, end.z, { head: marks.final, scrim: 0.7 });

    // Then a slow pull-back over the whole sheet while the long sections are read.
    const boxes = [{ ...HERO.centre, ...HERO.size }, ...figs.map((fig) => fig.box)];
    const x0 = Math.min(...boxes.map((b) => b.x - b.w / 2));
    const x1 = Math.max(...boxes.map((b) => b.x + b.w / 2));
    const y0 = Math.min(...boxes.map((b) => b.y - b.h / 2));
    const y1 = Math.max(...boxes.map((b) => b.y + b.h / 2));
    const zf = Math.min((0.9 * W) / (x1 - x0 + 700), (0.74 * H) / (y1 - y0 + 700));
    const count = Math.max(1, contentCount);
    for (let n = 1; n <= count; n++) {
        const t = smooth(n / count);
        add(end.u + n, lerp(end.x, (x0 + x1) / 2, t), lerp(end.y, (y0 + y1) / 2, t), Math.exp(lerp(Math.log(end.z), Math.log(zf), t)), { head: marks.final, scrim: 0.88 });
    }
    return { keys, pad };
}

export function mountBoard(root, { reduced = false } = {}) {
    const canvas = root.querySelector('canvas');
    const stage = root.querySelector('.journey__stage');
    const hud = root.querySelector('[data-board-hud]');
    const sections = [...root.querySelectorAll('[data-j]')];
    const ctx = canvas?.getContext('2d', { alpha: false });
    if (!ctx || !stage || !sections.length || reduced) return null;

    let cfg = {};
    try {
        cfg = JSON.parse(root.dataset.board || '{}');
    } catch { /* an empty sheet is still a sheet */ }

    const rtl = document.documentElement.dir === 'rtl';
    const mir = rtl ? 1 : -1; // the sheet is laid out for a right-to-left reader, and mirrored for the other
    const css = getComputedStyle(root);
    const ink = {
        bg: css.getPropertyValue('--board').trim() || '#0f172a',
        deep: css.getPropertyValue('--board-deep').trim() || '#16213b',
        line: css.getPropertyValue('--board-line').trim() || '#24abe3',
        text: '#f1f6fb',
    };

    const world = buildWorld(cfg, mir);
    const stations = [];
    sections.forEach((el, index) => {
        if (el.dataset.station) stations.push({ name: el.dataset.station, el, index });
    });
    world.figs.forEach((fig) => { fig.station = stations.find((s) => s.name === fig.kind) || null; });
    const launch = world.figs.find((fig) => fig.kind === 'launch' && fig.station) || null;
    const landingAt = sections.findIndex((el) => el.hasAttribute('data-landing'));
    const lastStation = stations[stations.length - 1];
    const contentCount = sections.length - (landingAt >= 0 ? landingAt + 1 : lastStation ? lastStation.index + 1 : 1);
    const timeline = createTimeline(sections);
    const ideas = cfg.ideas || [];
    // Where the rocket goes: the button, and the wrapper that lights up when it gets there.
    const target = root.querySelector('[data-rocket-target]');
    const pad = root.querySelector('[data-rocket-pad]');
    const rocketPath = measured(ROCKET);
    const rocketLen = rocketPath[rocketPath.length - 1].s;

    // ── Pictures: only the hero has any (one screenshot per idea) ──
    const pictures = new Map();
    const request = (url) => {
        if (!url || pictures.has(url)) return;
        const img = new Image();
        img.decoding = 'async';
        img.src = url;
        pictures.set(url, img);
    };
    const picture = (url) => {
        const img = url ? pictures.get(url) : null;
        return img && img.complete && img.naturalWidth ? img : null;
    };
    ideas.forEach((idea) => request(idea.image));

    const view = { W: 1, H: 1, dpr: 1, small: false };
    let keys = [];
    let liftOff = null;
    let path = {};
    const measure = () => {
        view.W = stage.clientWidth || window.innerWidth;
        view.H = window.innerHeight;
        view.small = view.W < 900;
        view.dpr = Math.min(window.devicePixelRatio || 1, 2);
        canvas.width = Math.round(view.W * view.dpr);
        canvas.height = Math.round(view.H * view.dpr);
        ({ keys, pad: liftOff } = buildKeys(world, landingAt, contentCount, view, rtl));
        const us = keys.map((k) => k.u);
        path = Object.fromEntries(['x', 'y', 'lz', 'rot'].map((name) => [name, curve(us, keys.map((k) => k[name]))]));
    };
    measure();

    const state = { u: 0, target: 0, tail: 0, tailTarget: 0, px: 0, py: 0, lx: 0, ly: 0 };
    const cam = { x: 0, y: 0, zoom: 1, rot: 0, head: 0, scrim: 0, shade: 0 };

    const locate = (u) => {
        const v = clamp(u, 0, keys[keys.length - 1].u);
        let i = 0;
        while (i < keys.length - 2 && v > keys[i + 1].u) i++;
        return { i, t: clamp((v - keys[i].u) / (keys[i + 1].u - keys[i].u || 1), 0, 1) };
    };
    const aim = (u, time) => {
        const { i, t } = locate(u);
        const b = keys[i];
        const c = keys[i + 1];
        cam.zoom = Math.exp(path.lz(i, t));
        cam.x = path.x(i, t) + (Math.sin(time * 0.31) * 5 + state.lx * 16) / cam.zoom;
        cam.y = path.y(i, t) + (Math.sin(time * 0.43) * 4 + state.ly * 10) / cam.zoom;
        cam.rot = path.rot(i, t);
        cam.head = lerp(b.head, c.head, smooth(t));
        cam.shade = lerp(b.shade, c.shade, smooth(t));
        cam.scrim = lerp(lerp(b.scrim, c.scrim, t), 0.34, state.tail);
    };

    // ── Drawing helpers (world space unless said otherwise) ──
    const px = (n) => n / cam.zoom; // n pixels on screen, whatever the zoom
    const pen = (width, min = 1, max = 6) => clamp(width * cam.zoom, min, max) / cam.zoom;
    const legible = (size) => size * cam.zoom >= 6;
    const toScreen = (x, y) => {
        const c = Math.cos(cam.rot);
        const s = Math.sin(cam.rot);
        const vx = (x - cam.x) * cam.zoom;
        const vy = (y - cam.y) * cam.zoom;
        return { x: view.W / 2 + vx * c - vy * s, y: view.H / 2 + vx * s + vy * c };
    };
    /** A box with the logo's cut corners. */
    const cutBox = (x, y, w, h, cut) => {
        const c = Math.min(cut, w / 2, h / 2);
        ctx.beginPath();
        ctx.moveTo(x + c, y);
        ctx.lineTo(x + w - c, y);
        ctx.lineTo(x + w, y + c);
        ctx.lineTo(x + w, y + h - c);
        ctx.lineTo(x + w - c, y + h);
        ctx.lineTo(x + c, y + h);
        ctx.lineTo(x, y + h - c);
        ctx.lineTo(x, y + c);
        ctx.closePath();
    };
    const octagon = (x, y, r) => {
        ctx.beginPath();
        for (let i = 0; i < 8; i++) {
            const a = (i / 8) * TAU + TAU / 16;
            ctx[i ? 'lineTo' : 'moveTo'](x + Math.cos(a) * r, y + Math.sin(a) * r);
        }
        ctx.closePath();
    };
    /** The logo's arrowhead: a solid triangle, pointing along (dx, dy). */
    const arrowhead = (x, y, dx, dy, size) => {
        const l = Math.hypot(dx, dy) || 1;
        const ux = dx / l;
        const uy = dy / l;
        ctx.beginPath();
        ctx.moveTo(x, y);
        ctx.lineTo(x - ux * size - uy * size * 0.62, y - uy * size + ux * size * 0.62);
        ctx.lineTo(x - ux * size + uy * size * 0.62, y - uy * size - ux * size * 0.62);
        ctx.closePath();
        ctx.fill();
    };
    const label = (text, x, y, size, { font = FONT.text, weight = 500, align = 'center', alpha = 1, color = ink.text, base = 'middle' } = {}) => {
        if (!text || !legible(size) || alpha <= 0.01) return;
        ctx.font = `${weight} ${size}px ${font}`;
        ctx.textAlign = align;
        ctx.textBaseline = base;
        ctx.globalAlpha = alpha;
        ctx.fillStyle = color;
        ctx.fillText(text, x, y);
        ctx.globalAlpha = 1;
    };
    const wrap = (text, width, size, font, weight) => {
        ctx.font = `${weight} ${size}px ${font}`;
        const lines = [];
        let line = '';
        String(text).split(/\s+/).forEach((word) => {
            const next = line ? `${line} ${word}` : word;
            if (line && ctx.measureText(next).width > width) {
                lines.push(line);
                line = word;
            } else {
                line = next;
            }
        });
        if (line) lines.push(line);
        return lines;
    };
    /** Strokes a measured polyline between two lengths along it. */
    const trace = (points, from, upTo) => {
        ctx.beginPath();
        const start = pointAt(points, from);
        ctx.moveTo(start.x, start.y);
        for (let i = 1; i < points.length; i++) {
            const p = points[i];
            if (p.s <= from) continue;
            if (p.s <= upTo) {
                ctx.lineTo(p.x, p.y);
                continue;
            }
            const end = pointAt(points, upTo);
            ctx.lineTo(end.x, end.y);
            break;
        }
    };

    function drawGrid() {
        // Two neighbouring levels of a ×5 grid, cross-faded, so zooming never
        // runs out of lines and never drowns in them.
        const reach = Math.hypot(view.W, view.H) / 2 / cam.zoom;
        [10, 50, 250, 1250, 6250].forEach((step) => {
            const onScreen = step * cam.zoom;
            const alpha = smooth(onScreen, 9, 46) * (1 - smooth(onScreen, 420, 1400) * 0.6) * 0.1;
            if (alpha < 0.006) return;
            ctx.beginPath();
            const x0 = Math.floor((cam.x - reach) / step) * step;
            const y0 = Math.floor((cam.y - reach) / step) * step;
            for (let x = x0; x <= cam.x + reach; x += step) {
                ctx.moveTo(x, cam.y - reach);
                ctx.lineTo(x, cam.y + reach);
            }
            for (let y = y0; y <= cam.y + reach; y += step) {
                ctx.moveTo(cam.x - reach, y);
                ctx.lineTo(cam.x + reach, y);
            }
            ctx.strokeStyle = ink.line;
            ctx.globalAlpha = alpha;
            ctx.lineWidth = px(1);
            ctx.stroke();
        });
        ctx.globalAlpha = 1;
    }

    function drawRoute(time) {
        const { pts, length } = world;
        ctx.strokeStyle = ink.line;
        ctx.lineJoin = 'miter';
        ctx.miterLimit = 3; // the tangle turns sharply: no spikes
        // Planned: everything ahead of the pen, in the logo's square dashes.
        ctx.lineCap = 'butt';
        ctx.lineWidth = pen(3.2, 1.4, 3);
        ctx.setLineDash([px(9), px(9)]);
        ctx.lineDashOffset = -time * px(24);
        ctx.globalAlpha = 0.42;
        trace(pts, cam.head, length);
        ctx.stroke();
        ctx.setLineDash([]);
        ctx.lineDashOffset = 0;
        // Built: solid, up to the pen.
        ctx.globalAlpha = 1;
        ctx.lineWidth = pen(6, 2, 6);
        trace(pts, 0, cam.head);
        ctx.stroke();
        return pointAt(pts, cam.head);
    }

    /** The pen: a glowing dot, the logo's. `r` is given when it is drawn on the screen rather than on the sheet. */
    function drawPen(x, y, time, r = clamp(13 * cam.zoom, 5.5, 16) / cam.zoom, alpha = 1) {
        if (alpha <= 0.01) return;
        ctx.globalAlpha = alpha;
        const halo = ctx.createRadialGradient(x, y, 0, x, y, r * 7);
        halo.addColorStop(0, 'rgba(36, 171, 227, 0.55)');
        halo.addColorStop(1, 'rgba(36, 171, 227, 0)');
        ctx.fillStyle = halo;
        ctx.beginPath();
        ctx.arc(x, y, r * 7, 0, TAU);
        ctx.fill();
        ctx.fillStyle = ink.line;
        ctx.beginPath();
        ctx.arc(x, y, r * (1 + Math.sin(time * 3) * 0.08), 0, TAU);
        ctx.fill();
        ctx.fillStyle = '#ffffff';
        ctx.beginPath();
        ctx.arc(x, y, r * 0.36, 0, TAU);
        ctx.fill();
        ctx.globalAlpha = 1;
    }

    /**
     * The hero: an idea is typed into a prompt, becomes a small diagram, and
     * the diagram becomes a real, built product. Then the next idea.
     */
    function drawHero(time) {
        if (!ideas.length || state.u > 1.5) return;
        const CYCLE = 9.4;
        const clockTime = Math.max(0, time);
        const idea = ideas[Math.floor(clockTime / CYCLE) % ideas.length];
        const t = clockTime % CYCLE;
        const out = 1 - smooth(t, 8.5, 9.2); // everything of this idea leaves together
        const flow = rtl ? -1 : 1; // the direction things read in
        const { prompt, win, port, nodeY } = HERO;

        // The prompt: a cut box with a tab, like a UML frame, and a caret.
        cutBox(prompt.x, prompt.y, prompt.w, prompt.h, 26);
        ctx.fillStyle = ink.deep;
        ctx.fill();
        ctx.strokeStyle = ink.line;
        ctx.lineJoin = 'miter';
        ctx.lineWidth = pen(4, 1.4, 4);
        ctx.stroke();
        const tabW = 190;
        const tabX = rtl ? prompt.x + prompt.w - tabW - 40 : prompt.x + 40;
        cutBox(tabX, prompt.y - 28, tabW, 56, 14);
        ctx.fillStyle = ink.line;
        ctx.fill();
        label(cfg.labels?.prompt, tabX + tabW / 2, prompt.y + 2, 28, { font: FONT.display, weight: 700, color: ink.bg });

        const chars = Array.from(idea.say || '');
        const typed = chars.slice(0, Math.round(chars.length * clamp((t - 0.35) / 2.1, 0, 1) * (1 - smooth(t, 8.5, 9.1)))).join('');
        const textX = prompt.x + (rtl ? prompt.w - 56 : 56);
        const textY = prompt.y + prompt.h / 2 + 12;
        label(typed, textX, textY, 46, { font: FONT.display, weight: 600, align: rtl ? 'right' : 'left' });
        ctx.font = `600 46px ${FONT.display}`;
        const typedW = typed ? ctx.measureText(typed).width : 0;
        if (t < 2.6 || Math.floor(time * 2.2) % 2 === 0) {
            ctx.fillStyle = ink.line;
            ctx.fillRect(textX + flow * (typedW + 12) - 2, textY - 30, 4, 58);
        }

        // The diagram: one octagon per part of the idea, joined by arrows.
        const steps = (idea.steps || []).slice(0, 4);
        const span = 660;
        const xs = steps.map((_, i) => -flow * span / 2 + flow * (steps.length > 1 ? (i * span) / (steps.length - 1) : span / 2));
        const born = (i) => 2.6 + i * 0.5;
        ctx.strokeStyle = ink.line;
        ctx.fillStyle = ink.line;
        ctx.lineWidth = pen(4, 1.6, 4);
        // Prompt → first node.
        const drop = smooth(t, 2.3, 2.7) * out;
        if (drop > 0.01 && xs.length) {
            ctx.globalAlpha = out;
            ctx.beginPath();
            ctx.moveTo(xs[0], prompt.y + prompt.h);
            ctx.lineTo(xs[0], lerp(prompt.y + prompt.h, nodeY - 66, drop));
            ctx.stroke();
        }
        steps.forEach((text, i) => {
            const on = smooth(t, born(i), born(i) + 0.3) * out;
            if (on <= 0.01) return;
            // Arrow to the next node.
            if (i < steps.length - 1) {
                const go = smooth(t, born(i) + 0.25, born(i) + 0.5) * out;
                const x1 = xs[i] + flow * 70;
                const x2 = xs[i + 1] - flow * 78;
                ctx.globalAlpha = out;
                ctx.beginPath();
                ctx.moveTo(x1, nodeY);
                ctx.lineTo(lerp(x1, x2, go), nodeY);
                ctx.stroke();
                if (go > 0.9) arrowhead(xs[i + 1] - flow * 64, nodeY, flow, 0, 22);
            }
            const k = 0.6 + on * 0.4;
            octagon(xs[i], nodeY, 62 * k);
            ctx.globalAlpha = on;
            ctx.fillStyle = ink.deep;
            ctx.fill();
            ctx.stroke();
            ctx.fillStyle = ink.line;
            ctx.direction = 'ltr';
            label(String(i + 1), xs[i], nodeY + 3, 36, { font: FONT.mono, weight: 800, color: ink.line, alpha: on });
            ctx.direction = rtl ? 'rtl' : 'ltr';
            label(text, xs[i], nodeY + 104, 31, { weight: 500, alpha: on });
            ctx.globalAlpha = 1;
        });

        // Last node → the window that gets built.
        const endX = xs.length ? xs[xs.length - 1] : 0;
        const link = smooth(t, born(steps.length - 1) + 0.5, born(steps.length - 1) + 0.9) * out;
        const built = smooth(t, 4.8, 6.6);
        const shown = Math.max(link, 0) ;
        if (link > 0.01) {
            ctx.globalAlpha = out;
            ctx.strokeStyle = ink.line;
            ctx.beginPath();
            ctx.moveTo(endX, nodeY + 150);
            ctx.lineTo(endX, lerp(nodeY + 150, win.y, link));
            ctx.stroke();
            ctx.globalAlpha = 1;
        }
        ctx.globalAlpha = Math.max(0.35, shown) * 1;
        windowFrame({ x: win.x, y: win.y, w: win.w, h: win.h, bar: win.bar, name: link > 0.5 && out > 0.5 ? idea.name : '', cut: 18 }, built * out, (bx, by, bw, bh) => {
            wireframe(bx, by, bw, bh);
            const shot = picture(idea.image);
            if (shot && built * out > 0.001) tiles(shot, bx, by, bw, bh, built * out, -flow, 8, 5);
        });
        ctx.globalAlpha = 1;

        // The port the line leaves by: a ring on the window's edge, on the side the line travels to.
        const portX = mir * port.x;
        ctx.beginPath();
        ctx.arc(portX, port.y, 22, 0, TAU);
        ctx.fillStyle = ink.bg;
        ctx.fill();
        ctx.strokeStyle = ink.line;
        ctx.lineWidth = pen(5, 1.6, 5);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(portX + mir * 22, port.y);
        ctx.lineTo(mir * win.x, port.y);
        ctx.stroke();
    }

    /** A site before it exists: boxes and lines. Right-aligned on the Arabic sheet. */
    function wireframe(x, y, w, h) {
        const sx = (f) => (rtl ? x + w - f * w : x + f * w); // f: 0 at the reading edge
        const box = (f, t, fw, fh) => {
            const left = rtl ? sx(f) - fw * w : sx(f);
            ctx.rect(left, y + t * h, fw * w, fh * h);
        };
        const before = ctx.globalAlpha;
        ctx.strokeStyle = ink.line;
        ctx.lineWidth = pen(2, 1, 2.5);
        ctx.globalAlpha = before * 0.55;
        ctx.beginPath();
        ctx.moveTo(x, y + 0.085 * h);
        ctx.lineTo(x + w, y + 0.085 * h);
        box(0.04, 0.025, 0.07, 0.036);
        [0.56, 0.65, 0.74, 0.83].forEach((f) => box(f, 0.036, 0.06, 0.012));
        box(0.06, 0.2, 0.36, 0.085);
        box(0.06, 0.32, 0.26, 0.085);
        [0.47, 0.515, 0.56].forEach((t, i) => box(0.06, t, 0.3 - i * 0.05, 0.012));
        box(0.06, 0.64, 0.13, 0.062);
        box(0.21, 0.64, 0.13, 0.062);
        const pw = 0.4 * w;
        const ph = 0.56 * h;
        const pxl = rtl ? x + 0.06 * w : x + w - 0.06 * w - pw;
        const pyt = y + 0.17 * h;
        ctx.rect(pxl, pyt, pw, ph);
        ctx.moveTo(pxl, pyt);
        ctx.lineTo(pxl + pw, pyt + ph);
        ctx.moveTo(pxl + pw, pyt);
        ctx.lineTo(pxl, pyt + ph);
        [0.06, 0.37, 0.68].forEach((f) => box(f, 0.8, 0.26, 0.13));
        ctx.stroke();
        ctx.globalAlpha = before;
    }

    /**
     * Draws `image` into a rectangle, built piece by piece: a grid of tiles
     * that arrive in a diagonal wave from the corner the pen came in at.
     */
    function tiles(image, x, y, w, h, built, from = 1, cols = 10, rows = 6) {
        const before = ctx.globalAlpha;
        // Crop the top of the image to the rectangle's shape.
        const srcH = Math.min(image.naturalHeight, image.naturalWidth * (h / w));
        if (built >= 0.999) {
            ctx.drawImage(image, 0, 0, image.naturalWidth, srcH, x, y, w, h);
            return;
        }
        const tw = w / cols;
        const th = h / rows;
        const sw = image.naturalWidth / cols;
        const sh = srcH / rows;
        for (let r = 0; r < rows; r++) {
            for (let c = 0; c < cols; c++) {
                const order = ((from > 0 ? cols - 1 - c : c) + r) / (cols + rows - 2);
                const p = smooth(built, order * 0.72, order * 0.72 + 0.28);
                if (p <= 0) continue;
                const k = 0.55 + p * 0.45;
                const cx = x + (c + 0.5) * tw;
                const cy = y + (r + 0.5) * th;
                ctx.globalAlpha = before * p;
                ctx.drawImage(image, c * sw, r * sh, sw, sh, cx - (tw * k) / 2, cy - (th * k) / 2 - (1 - p) * 40, tw * k + 0.5, th * k + 0.5);
            }
        }
        ctx.globalAlpha = before;
    }

    /** Once built, a thing lifts off the paper: its body casts a shadow. */
    const lifted = (built, fill) => {
        ctx.save();
        ctx.shadowColor = 'rgba(0, 0, 0, 0.55)';
        ctx.shadowBlur = 70 * cam.zoom * view.dpr * built;
        ctx.shadowOffsetY = 34 * cam.zoom * view.dpr * built;
        ctx.fillStyle = ink.deep;
        fill();
        ctx.restore();
    };

    /** A browser window: a bar with three rings, and a body that is a drawing until it is built. */
    function windowFrame({ x, y, w, h, bar, name, cut = 26 }, built, body) {
        const total = h + bar;
        lifted(built, () => {
            cutBox(x, y, w, total, cut);
            ctx.fill();
        });

        ctx.save();
        cutBox(x, y, w, total, cut);
        ctx.clip();
        body(x, y + bar, w, h);
        ctx.fillStyle = ink.deep;
        ctx.fillRect(x, y, w, bar);
        ctx.strokeStyle = ink.line;
        ctx.lineWidth = pen(2, 1, 2.2);
        const r = bar * 0.16;
        [0, 1, 2].forEach((n) => {
            ctx.beginPath();
            ctx.arc(rtl ? x + w - bar * 0.75 - n * r * 3.6 : x + bar * 0.75 + n * r * 3.6, y + bar / 2, r, 0, TAU);
            ctx.stroke();
        });
        ctx.beginPath();
        ctx.moveTo(x, y + bar);
        ctx.lineTo(x + w, y + bar);
        ctx.stroke();
        label(name, x + w / 2, y + bar / 2 + 2, bar * 0.42, { font: FONT.display, weight: 600, alpha: 0.85 * ctx.globalAlpha });
        ctx.restore();

        ctx.strokeStyle = ink.line;
        ctx.lineWidth = pen(4, 1.4, 4);
        ctx.lineJoin = 'miter';
        cutBox(x, y, w, total, cut);
        ctx.stroke();
    }

    // ── The drawings on the line ──
    /** A point of a drawing, on the sheet. */
    const at = (fig, lx, ly) => ({ x: fig.x + mir * lx, y: fig.y + ly });
    /** Draws in a drawing's own coordinates, mirrored with the sheet. Shapes only: text would come out backwards. */
    const inFigure = (fig, fn) => {
        ctx.save();
        ctx.translate(fig.x, fig.y);
        ctx.scale(mir, 1);
        fn();
        ctx.restore();
    };
    /** How much of a drawing's own line the pen has drawn. */
    const along = (fig) => {
        const a = world.marks[`f${fig.j}in`];
        const b = world.marks[`f${fig.j}out`];
        return clamp((cam.head - a) / (b - a || 1), 0, 1);
    };
    const tick = (x, y, k, color) => {
        ctx.beginPath();
        ctx.moveTo(x - 0.42 * k, y + 0.02 * k);
        ctx.lineTo(x - 0.13 * k, y + 0.31 * k);
        ctx.lineTo(x + 0.44 * k, y - 0.28 * k);
        ctx.strokeStyle = color;
        ctx.lineWidth = k * 0.2;
        ctx.lineCap = 'square';
        ctx.stroke();
        ctx.lineCap = 'butt';
    };
    /** A passed check: the logo's octagon, filled, with a tick. It lands a little too big and settles. */
    const badge = (x, y, r, on) => {
        if (on <= 0.01) return;
        const k = r * (0.5 + on * 0.5 + Math.sin(on * Math.PI) * 0.16);
        ctx.globalAlpha = Math.min(1, on * 1.6);
        octagon(x, y, k);
        ctx.fillStyle = ink.line;
        ctx.fill();
        tick(x, y, k, ink.bg);
        ctx.globalAlpha = 1;
    };

    // The idea: the founder's questions, round the tangle they come out of.
    const ASKS = [[340, -505, 0.12], [-340, -505, 0.36], [340, 520, 0.58], [-340, 520, 0.8]];
    function drawIdea(fig, local, time) {
        const drawn = along(fig);
        const size = view.small ? 66 : 46;
        ASKS.forEach(([lx, ly, when], n) => {
            const on = smooth(drawn, when, when + 0.1);
            const text = (cfg.questions || [])[n];
            if (!text || on <= 0.01) return;
            const p = at(fig, lx, ly + Math.sin(time * 0.9 + n * 1.7) * 9 - (1 - on) * 34 * Math.sign(ly));
            label(text, p.x, p.y, size, { font: FONT.display, weight: 600, alpha: on * 0.92 });
        });
    }

    // The analysis: each row of the line is one answer. A node on the row turns
    // into a check as the pen passes it.
    function drawAnalysis(fig) {
        const size = view.small ? 64 : 48;
        ROWS.forEach(([lx, ly], n) => {
            const mark = world.marks[`f${fig.j}r${n}`];
            const near = smooth(cam.head, mark - 560, mark - 140);
            const done = smooth(cam.head, mark - 40, mark + 40);
            if (near <= 0.01) return;
            const p = at(fig, lx, ly);
            octagon(p.x, p.y, 34 + done * 6);
            ctx.globalAlpha = near;
            ctx.fillStyle = done > 0.5 ? ink.line : ink.bg;
            ctx.fill();
            ctx.strokeStyle = ink.line;
            ctx.lineJoin = 'miter';
            ctx.lineWidth = pen(4, 1.6, 4.5);
            ctx.stroke();
            if (done > 0.5) tick(p.x, p.y, 34, ink.bg);
            ctx.globalAlpha = 1;
            label((cfg.rows || [])[n], p.x, p.y - 84, size, { font: FONT.display, weight: 700, alpha: near * (0.55 + done * 0.45) });
        });
    }

    // The screen that gets designed, then built. An invented interface: a logo,
    // a menu, a headline, a button, a chart and three cards. tone: what a piece
    // is made of once it is built.
    const PIECES = (() => {
        const list = [
            { x: 410, y: -262, w: 100, h: 36, tone: 'accent', cut: 10 },
            { x: -510, y: -250, w: 70, h: 12, tone: 'dim' },
            { x: -420, y: -250, w: 70, h: 12, tone: 'dim' },
            { x: -330, y: -250, w: 70, h: 12, tone: 'dim' },
            { x: 140, y: -180, w: 370, h: 44, tone: 'text' },
            { x: 250, y: -116, w: 260, h: 32, tone: 'text' },
            { x: 200, y: -56, w: 310, h: 12, tone: 'dim' },
            { x: 280, y: -30, w: 230, h: 12, tone: 'dim' },
            { x: 350, y: 20, w: 160, h: 52, tone: 'accent', cut: 14 },
            { x: -510, y: -190, w: 570, h: 280, tone: 'panel', cut: 22, holds: 'chart' },
            { x: 210, y: 150, w: 300, h: 190, tone: 'panel', cut: 18, holds: 'card' },
            { x: -150, y: 150, w: 300, h: 190, tone: 'panel', cut: 18, holds: 'card' },
            { x: -510, y: 150, w: 300, h: 190, tone: 'panel', cut: 18, holds: 'card' },
        ];
        return list.map((piece, n) => ({ ...piece, order: n / list.length }));
    })();
    const HANDSET = (() => {
        const { x, y } = PHONE;
        return [
            { x: x + 80, y: y + 20, w: 60, h: 8, tone: 'dim' },
            { x: x + 20, y: y + 50, w: 180, h: 130, tone: 'panel', cut: 12 },
            { x: x + 60, y: y + 204, w: 140, h: 16, tone: 'text' },
            { x: x + 90, y: y + 236, w: 110, h: 10, tone: 'dim' },
            { x: x + 70, y: y + 258, w: 130, h: 10, tone: 'dim' },
            { x: x + 20, y: y + 300, w: 180, h: 44, tone: 'accent', cut: 12 },
            { x: x + 20, y: y + 366, w: 82, h: 40, tone: 'panel', cut: 8 },
            { x: x + 118, y: y + 366, w: 82, h: 40, tone: 'panel', cut: 8 },
        ];
    })();
    const outline = (x, y, w, h, cut) => {
        if (cut) {
            cutBox(x, y, w, h, cut);
            return;
        }
        ctx.beginPath();
        ctx.rect(x, y, w, h);
    };
    /** A piece as a design: its outline, dashed. */
    function planned(piece, on) {
        if (on <= 0.01) return;
        ctx.globalAlpha = on * 0.85;
        ctx.strokeStyle = ink.line;
        ctx.lineWidth = pen(2.4, 1.1, 2.6);
        ctx.setLineDash([px(7), px(6)]);
        outline(piece.x, piece.y, piece.w, piece.h, piece.cut);
        if (piece.holds === 'chart') {
            // A picture not chosen yet: a box with its diagonals.
            ctx.moveTo(piece.x + 22, piece.y + 22);
            ctx.lineTo(piece.x + piece.w - 22, piece.y + piece.h - 22);
            ctx.moveTo(piece.x + piece.w - 22, piece.y + 22);
            ctx.lineTo(piece.x + 22, piece.y + piece.h - 22);
        }
        ctx.stroke();
        ctx.setLineDash([]);
        ctx.globalAlpha = 1;
    }
    /** A piece once built: solid, dropping into place. */
    function built(piece, on) {
        if (on <= 0.01) return;
        const k = 0.74 + on * 0.26;
        const w = piece.w * k;
        const h = piece.h * k;
        const x = piece.x + (piece.w - w) / 2;
        const y = piece.y + (piece.h - h) / 2 - (1 - on) * 30;
        ctx.globalAlpha = on * (piece.tone === 'dim' ? 0.42 : 1);
        outline(x, y, w, h, piece.cut ? piece.cut * k : 0);
        ctx.fillStyle = piece.tone === 'accent' ? ink.line : piece.tone === 'panel' ? ink.deep : ink.text;
        ctx.fill();
        if (piece.tone !== 'panel') {
            ctx.globalAlpha = 1;
            return;
        }
        ctx.strokeStyle = ink.line;
        ctx.lineJoin = 'miter';
        ctx.lineWidth = pen(2.6, 1.2, 3);
        ctx.stroke();
        const grown = smooth(on, 0.5, 1);
        if (piece.holds === 'chart') {
            // Bars that grow from a baseline, the last one the tallest.
            ctx.fillStyle = ink.line;
            [70, 112, 92, 150, 128, 196].forEach((tall, n) => {
                ctx.globalAlpha = on * (n === 5 ? 1 : 0.5);
                ctx.fillRect(x + 44 + n * 62, y + h - 34 - tall * grown, 38, tall * grown);
            });
            ctx.globalAlpha = on * 0.42;
            ctx.fillStyle = ink.text;
            ctx.fillRect(x + w - 150, y + 30, 116, 12);
            ctx.fillRect(x + w - 110, y + 54, 76, 12);
        }
        if (piece.holds === 'card') {
            ctx.globalAlpha = on;
            ctx.beginPath();
            ctx.arc(x + w - 50, y + 52, 20, 0, TAU);
            ctx.stroke();
            ctx.fillStyle = ink.text;
            ctx.globalAlpha = on * 0.9;
            ctx.fillRect(x + w - 30 - 190 * grown, y + 98, 190 * grown, 16);
            ctx.globalAlpha = on * 0.42;
            ctx.fillRect(x + w - 30 - 140 * grown, y + 134, 140 * grown, 12);
        }
        ctx.globalAlpha = 1;
    }
    /** The three rings of a window, on its bar. */
    function windowRings(on) {
        if (on <= 0.01) return;
        ctx.globalAlpha = on;
        ctx.strokeStyle = ink.line;
        ctx.lineWidth = pen(2.6, 1.2, 3);
        [0, 1, 2].forEach((n) => {
            ctx.beginPath();
            ctx.arc(SCREEN.x + SCREEN.w - 62 - n * 46, SCREEN.y + SCREEN.bar / 2, 13, 0, TAU);
            ctx.stroke();
        });
        ctx.globalAlpha = 1;
    }
    /** The phone leaning on the screen's corner: the same interface, small. */
    function handset(on, solid) {
        if (on <= 0.01) return;
        const { x, y, w, h, cut } = PHONE;
        const lift = (1 - on) * 40;
        ctx.save();
        ctx.translate(0, lift);
        ctx.globalAlpha = on;
        cutBox(x, y, w, h, cut);
        ctx.fillStyle = ink.bg;
        ctx.fill();
        ctx.strokeStyle = ink.line;
        ctx.lineJoin = 'miter';
        ctx.lineWidth = pen(4, 1.6, 4.5);
        if (!solid) ctx.setLineDash([px(9), px(7)]);
        ctx.stroke();
        ctx.setLineDash([]);
        HANDSET.forEach((piece) => (solid ? built(piece, on) : planned(piece, on)));
        ctx.restore();
        ctx.globalAlpha = 1;
    }

    function drawDesign(fig, local) {
        const drawn = along(fig);
        const laid = local >= 1 ? 1 : smooth(local, 0.48, 0.82);
        inFigure(fig, () => {
            windowRings(smooth(drawn, 0.3, 0.42));
            PIECES.forEach((piece) => planned(piece, smooth(laid, piece.order * 0.8, piece.order * 0.8 + 0.2)));
            handset(local >= 1 ? 1 : smooth(local, 0.72, 0.86), false);
        });
    }

    function drawBuild(fig, local) {
        const drawn = along(fig);
        const made = local >= 1 ? 1 : smooth(local, 0.42, 0.7);
        const tested = local >= 1 ? 1 : clamp((local - 0.7) / 0.16, 0, 1);
        const top = SCREEN.y - 40;
        const bottom = PHONE.y + PHONE.h + 40;
        inFigure(fig, () => {
            windowRings(smooth(drawn, 0.3, 0.42));
            PIECES.forEach((piece) => built(piece, smooth(made, piece.order * 0.78, piece.order * 0.78 + 0.22)));
            handset(local >= 1 ? 1 : smooth(local, 0.6, 0.72), true);
            if (tested > 0 && tested < 1) {
                // The check: a white line that reads the whole thing, top to bottom.
                const y = lerp(top, bottom, tested);
                ctx.strokeStyle = '#ffffff';
                ctx.lineWidth = pen(5, 2, 5);
                ctx.beginPath();
                ctx.moveTo(PHONE.x - 50, y);
                ctx.lineTo(SCREEN.x + SCREEN.w + 50, y);
                ctx.stroke();
            }
        });
        // Passed, on the computer and on the phone.
        const span = bottom - top;
        const passedPhone = at(fig, PHONE.x, PHONE.y);
        const passedScreen = at(fig, SCREEN.x + SCREEN.w, SCREEN.y + SCREEN.h);
        badge(passedPhone.x, passedPhone.y, 62, smooth(tested, (PHONE.y - top) / span, (PHONE.y - top) / span + 0.12));
        badge(passedScreen.x, passedScreen.y, 62, smooth(tested, (SCREEN.y + SCREEN.h - top) / span, (SCREEN.y + SCREEN.h - top) / span + 0.12));
    }

    // The launch has nothing of its own on the sheet: the line ends in the rocket, and the rocket is drawn on the screen (drawRocket).
    const drawers = { idea: drawIdea, analysis: drawAnalysis, design: drawDesign, build: drawBuild };
    let focus = 0; // 1 while the camera is standing at some stop and its card is being read
    function drawFigures(time) {
        world.figs.forEach((fig) => {
            const s = toScreen(fig.box.x, fig.box.y);
            const reach = (Math.max(fig.box.w, fig.box.h) * cam.zoom) / 2 + 320;
            if (s.x < -reach || s.x > view.W + reach || s.y < -reach || s.y > view.H + reach) return;
            const local = fig.station ? state.u - fig.station.index : 0;
            // Its name above it, until the HTML card takes over at the stop.
            const begun = smooth(cam.head, world.marks[`f${fig.j}in`] - 1200, world.marks[`f${fig.j}in`]);
            const left = fig === launch ? 1 - smooth(local, FLY.fire[0], FLY.from) : 1;
            label(fig.title, fig.box.x, fig.box.y - fig.box.h / 2 - 70, 104, { font: FONT.display, weight: 800, alpha: (0.35 + begun * 0.65) * (1 - focus) * left * (1 - smooth(cam.scrim, 0.05, 0.5)) });
            drawers[fig.kind]?.(fig, local, time);
        });
    }

    // ── The rocket ──
    // Drawn by the pen where the line ends, then it takes the pen with it (the
    // dot becomes its window) and flies, across the screen, to the button.
    const cubic = (a, b, c, d, t) => {
        const m = 1 - t;
        return m * m * m * a + 3 * m * m * t * b + 3 * m * t * t * c + t * t * t * d;
    };
    const slope = (a, b, c, d, t) => {
        const m = 1 - t;
        return 3 * m * m * (b - a) + 6 * m * t * (c - b) + 3 * t * t * (d - c);
    };
    let lit = false;
    const light = (on) => {
        if (on === lit) return;
        lit = on;
        pad?.classList.toggle('is-lit', on);
    };
    /** True while the rocket has the pen, so the line's own pen is not drawn as well. */
    const rocketHasPen = () => Boolean(launch) && state.u - launch.station.index > FLY.trace[0];

    function drawRocket(time) {
        if (!launch) return;
        const p = state.u - launch.station.index;
        const raw = (p - FLY.from) / (FLY.to - FLY.from); // 0 at lift-off, 1 at the button
        light(raw >= 0.9); // by then it is behind the button
        if (p <= FLY.trace[0] || raw > 1.3) return;

        const { W, H, dpr } = view;
        const traced = clamp((p - FLY.trace[0]) / (FLY.trace[1] - FLY.trace[0]), 0, 1);
        const hop = smooth(p, FLY.hop[0], FLY.hop[1]);
        const fire = smooth(p, FLY.fire[0], FLY.fire[1]);
        const q = smooth(clamp(raw, 0, 1));
        const gone = smooth(q, 0.93, 1);

        // From where it stands, up, over, and down onto the button. Once it is
        // off the ground the flight is laid out from where the pad stood, not
        // from where the page has since carried it.
        const from = toScreen(launch.x, launch.y);
        if (liftOff && raw > 0) {
            const free = smooth(raw, 0, 0.24);
            from.x = lerp(from.x, W / 2 + (launch.x - liftOff.x) * liftOff.z, free);
            from.y = lerp(from.y, H / 2 + (launch.y - liftOff.y) * liftOff.z, free);
        }
        let to = { x: W / 2, y: -H * 0.4 };
        if (target) {
            const box = target.getBoundingClientRect();
            const sheet = canvas.getBoundingClientRect();
            to = { x: box.left + box.width / 2 - sheet.left, y: Math.min(box.top + box.height / 2 - sheet.top, H * 1.6) };
        }
        // It leaves straight up the line it stands on. On a narrow screen the
        // button is right below, so it comes down from well out to one side
        // instead of turning on the spot.
        const lean = Math.sign(to.x - from.x || 1);
        const xs = [from.x, from.x, to.x - lean * (view.small ? 0.9 : 0.1) * W, to.x];
        const ys = [from.y, from.y - (view.small ? 0.34 : 0.62) * H, to.y - (view.small ? 0.5 : 0.46) * H, to.y];
        const x = cubic(...xs, q);
        const y = cubic(...ys, q);
        const big = liftOff && raw > 0 ? lerp(cam.zoom, liftOff.z, smooth(raw, 0, 0.24)) : cam.zoom;
        const k = Math.exp(lerp(Math.log(big), Math.log(Math.min(0.1, big)), smooth(q, 0.3, 1)));
        const shake = fire * (1 - smooth(q, 0, 0.2));

        // The line it flies along: the line it stood on, carried on through
        // its centre. It stays tied to the end of the line on the sheet while
        // the page carries that away.
        const pad = toScreen(launch.x, launch.y);
        const pull = { x: pad.x - from.x, y: pad.y - from.y };
        const point = (a) => {
            const held = q > 0 ? (1 - a / q) ** 2 : 1;
            return { x: cubic(...xs, a) + pull.x * held, y: cubic(...ys, a) + pull.y * held };
        };
        // It lies along that line: its centre is on it, and so is the middle
        // of its nozzle, which is found by walking back along the line the
        // length of its tail. While it is still close to the pad that point
        // is on the straight stretch under it.
        const reach = ROCKET[0][1] * k;
        let aft = null;
        let before = { x, y };
        for (let n = 1; n <= 30 && q > 0; n++) {
            const now = point(q * (1 - n / 30));
            const far = Math.hypot(now.x - x, now.y - y);
            if (far >= reach) {
                const near = Math.hypot(before.x - x, before.y - y);
                const t = (reach - near) / (far - near || 1);
                aft = { x: lerp(before.x, now.x, t), y: lerp(before.y, now.y, t) };
                break;
            }
            before = now;
        }
        if (!aft) aft = { x: pad.x, y: y + Math.sqrt(Math.max(0, reach * reach - (pad.x - x) ** 2)) };
        const angle = Math.atan2(y - aft.y, x - aft.x) + Math.PI / 2;

        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        ctx.strokeStyle = ink.line;
        ctx.lineJoin = 'miter';
        ctx.miterLimit = 3;

        // It lets go of the pad a little after lift-off, and what is left
        // behind fades from the far end and is gone soon after it lands.
        const tail = smooth(clamp(raw - 0.26, 0, 1));
        if (q > 0.002 && tail < q) {
            const loose = smooth(raw, 0.26, 0.42); // 0 while it is still tied to the end of the line
            const parts = 30;
            ctx.lineWidth = clamp(6 * cam.zoom, 2, 6); // the line's own weight
            if (loose < 1) {
                // From the nozzle's place on the pad up to where the centre was.
                ctx.globalAlpha = 1 - loose;
                ctx.beginPath();
                ctx.moveTo(pad.x, pad.y + ROCKET[0][1] * cam.zoom);
                ctx.lineTo(pad.x, pad.y);
                ctx.stroke();
            }
            for (let n = 0; n < parts; n++) {
                const from2 = point(lerp(tail, q, n / parts));
                const to2 = point(lerp(tail, q, (n + 1) / parts));
                ctx.globalAlpha = lerp(1, smooth((n + 1) / parts, 0, 0.5), loose) * (1 - gone * 0.5);
                ctx.beginPath();
                ctx.moveTo(from2.x, from2.y);
                ctx.lineTo(to2.x, to2.y);
                ctx.stroke();
            }
            ctx.globalAlpha = 1;
        }

        const turn = angle + cam.rot * (1 - q);
        const ox = x + Math.sin(time * 41) * 1.6 * shake;
        const oy = y + Math.cos(time * 33) * 1.2 * shake;
        ctx.save();
        ctx.translate(ox, oy);
        ctx.rotate(turn);
        ctx.scale(k * mir, k);
        ctx.globalAlpha = 1 - gone;
        ctx.lineWidth = clamp(6 * k, 1.8, 6) / k;
        if (fire > 0.01) {
            // The burn: a dashed line each side of the line it rides on.
            ctx.setLineDash([24, 16]);
            ctx.lineDashOffset = -time * 280;
            [-56, 56].forEach((fx, n) => {
                const len = 230 * fire * (0.74 + 0.26 * Math.sin(time * 17 + n * 2.1));
                ctx.beginPath();
                ctx.moveTo(fx, 352);
                ctx.lineTo(fx * 1.3, 352 + len);
                ctx.stroke();
            });
            ctx.setLineDash([]);
            ctx.lineDashOffset = 0;
        }
        if (hop > 0.01) {
            // Once the stroke closes it is a body: it hides what is behind it.
            ctx.beginPath();
            ROCKET.forEach(([rx, ry], n) => ctx[n ? 'lineTo' : 'moveTo'](rx, ry));
            ctx.closePath();
            ctx.fillStyle = ink.bg;
            ctx.globalAlpha = hop * (1 - gone);
            ctx.fill();
            ctx.beginPath();
            ctx.moveTo(-130, -290);
            ctx.lineTo(130, -290);
            ctx.moveTo(-130, 130);
            ctx.lineTo(130, 130);
            ctx.moveTo(WINDOW.x + WINDOW.r, WINDOW.y);
            ctx.arc(WINDOW.x, WINDOW.y, WINDOW.r, 0, TAU);
            ctx.stroke();
            ctx.globalAlpha = 1 - gone;
        }
        trace(rocketPath, 0, traced * rocketLen);
        ctx.stroke();
        ctx.restore();
        ctx.globalAlpha = 1;

        // The pen: round the outline while it is drawn, then into the window.
        const tip = traced < 1 ? pointAt(rocketPath, traced * rocketLen) : { x: lerp(ROCKET[0][0], WINDOW.x, hop), y: lerp(ROCKET[0][1], WINDOW.y, hop) };
        const c = Math.cos(turn);
        const s = Math.sin(turn);
        const tx = tip.x * k * mir;
        const ty = tip.y * k;
        drawPen(ox + tx * c - ty * s, oy + tx * s + ty * c, time, Math.max(2.4, clamp(13 * k, 0, 16)), 1 - gone);
    }

    function draw(time) {
        const { W, H, dpr } = view;
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        ctx.globalAlpha = 1;
        ctx.globalCompositeOperation = 'source-over';
        ctx.fillStyle = ink.bg;
        ctx.fillRect(0, 0, W, H);
        ctx.direction = rtl ? 'rtl' : 'ltr';

        ctx.translate(W / 2, H / 2);
        ctx.rotate(cam.rot);
        ctx.scale(cam.zoom, cam.zoom);
        ctx.translate(-cam.x, -cam.y);

        drawGrid();
        const head = drawRoute(time);
        drawHero(time);
        focus = stations.reduce((most, st) => Math.max(most, smooth(state.u - st.index, 0.18, 0.3) * (1 - smooth(state.u - st.index, 0.92, 1))), 0);
        drawFigures(time);
        if (!rocketHasPen()) drawPen(head.x, head.y, time);
        drawRocket(time);

        stage.style.setProperty('--scrim', cam.scrim.toFixed(3));
        stage.style.setProperty('--shade', cam.shade.toFixed(3));
        if (hud) {
            const n = (v) => String(Math.round(Math.abs(v))).padStart(5, '0');
            hud.textContent = `X ${n(cam.x)}  Y ${n(cam.y)}  ×${cam.zoom.toFixed(2)}`;
        }
    }

    const readScroll = () => {
        const { u, tail } = timeline.read();
        state.target = u;
        state.tailTarget = tail;
    };
    window.addEventListener('scroll', readScroll, { passive: true });
    window.addEventListener('pointermove', (e) => {
        state.px = (e.clientX / window.innerWidth) * 2 - 1;
        state.py = (e.clientY / window.innerHeight) * 2 - 1;
    }, { passive: true });

    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            measure();
            readScroll();
        }, 120);
    }, { passive: true });

    let running = false;
    let onScreen = true;
    let started = false;
    let manual = false; // set by seek(): the capture tool drives the timeline itself
    let last = 0;
    const clock = { t: 0 };
    const frame = (now) => {
        if (!running) return;
        // A frame's timestamp can be a hair earlier than the performance.now()
        // taken when the loop (re)started, so the step is clamped at zero.
        const dt = clamp((now - last) / 1000, 0, 0.05);
        last = now;
        clock.t += dt;
        if (!manual) state.u += (state.target - state.u) * (1 - Math.exp(-dt * 3.6));
        state.tail += (state.tailTarget - state.tail) * (1 - Math.exp(-dt * 3.6));
        state.lx += (state.px - state.lx) * (1 - Math.exp(-dt * 2.2));
        state.ly += (state.py - state.ly) * (1 - Math.exp(-dt * 2.2));
        aim(state.u, clock.t);
        draw(clock.t);
        requestAnimationFrame(frame);
    };
    const sync = () => {
        const should = started && onScreen && !document.hidden;
        if (should && !running) {
            running = true;
            last = performance.now();
            requestAnimationFrame(frame);
        }
        running = should;
    };
    new IntersectionObserver(([entry]) => {
        onScreen = entry.isIntersecting;
        sync();
    }).observe(stage);
    document.addEventListener('visibilitychange', sync);

    const api = {
        start() {
            if (started) return;
            started = true;
            readScroll();
            state.u = state.target;
            root.classList.add('is-ready');
            sync();
        },
        /** Jump to a point on the timeline and draw one frame (used for stills). */
        seek(u, time = clock.t) {
            manual = true;
            state.u = state.target = u;
            clock.t = time;
            aim(u, time);
            draw(time);
        },
        yFor: (u) => timeline.yFor(u),
        length: keys[keys.length - 1].u,
        stations: stations.map((s) => ({ name: s.name, index: s.index })),
        landing: landingAt,
    };
    window.__journey = api;
    return api;
}
