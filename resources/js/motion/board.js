/**
 * board.js — the home page as one flat drawing, and a camera that travels it.
 *
 * The drawing speaks the logo's language without copying the logo: one
 * weight of line, corners cut at 45°, octagons, dashed paths, ring nodes, a
 * filled dot, a solid arrowhead. Where a shape has a meaning in UML it is
 * used with that meaning (notes, an actor, a decision, a final state,
 * realization arrows).
 *
 * The story, in the order the camera meets it:
 *
 *   the hero      an idea is typed, becomes a diagram, becomes a real product (it loops)
 *   the roadmap   four steps down a line, each answering a founder's question
 *   the projects  four windows on a staircase; each is a drawing until the pen arrives
 *   the end       a final state: launch. Then a pull-back over the whole sheet.
 *
 * A pen travels the route. Ahead of it the line is dashed (planned); behind
 * it, solid (built).
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

const PLOT = { w: 1500, h: 938, bar: 58 };
const PHONE = { w: 330, h: 714 };
// The hero scene: a prompt, a row of nodes, a small window, and the port the route leaves by.
const HERO = {
    prompt: { x: -450, y: -440, w: 900, h: 130 },
    nodeY: -150,
    win: { x: -320, y: 70, w: 640, h: 400, bar: 40 },
    port: { x: -430, y: 290 },
    centre: { x: 0, y: 20 },
    size: { w: 960, h: 1040 },
};
// Where the four steps and the four projects stand. Free composition: a line
// that drops, jogs at 45°, drops again, then a staircase of windows.
const STEPS = [[-1500, 1300], [-1500, 2100], [-2000, 3500], [-2000, 4300]];
const BEND = { x: -1750, y: 2750 };
const SITES = [[-4300, 5300], [-7200, 6300], [-10100, 7300], [-13000, 8300]];
const FINAL = { x: -14950, y: 8300 };
const FONT = {
    display: '"Alexandria Variable", "Readex Pro Variable", sans-serif',
    text: '"Readex Pro Variable", "Alexandria Variable", sans-serif',
    mono: '"JetBrains Mono Variable", ui-monospace, monospace',
};

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

/** Lays out the sheet: the pen's route, and what stands on it. */
function buildWorld(cfg) {
    const pts = [];
    const marks = {};
    let len = 0;
    const to = (x, y) => {
        if (pts.length) {
            const p = pts[pts.length - 1];
            len += Math.hypot(x - p.x, y - p.y);
        }
        pts.push({ x, y, s: len });
    };
    const mark = (name) => { marks[name] = len; };

    // Out of the hero, then down.
    to(HERO.port.x, HERO.port.y);
    to(-1200, HERO.port.y);
    to(-1500, HERO.port.y + 300);

    const nodes = (cfg.steps || []).slice(0, STEPS.length).map((step, i) => {
        const [x, y] = STEPS[i];
        if (i === 2) {
            // The 45° jog between the two halves of the roadmap carries the decision.
            to(-1500, 2500);
            to(BEND.x, BEND.y);
            mark('bend');
            to(-2000, 3000);
        }
        to(x, y);
        return { ...step, n: i + 1, question: (cfg.questions || [])[i], x, y, at: len };
    });
    if (marks.bend === undefined) mark('bend');
    to(-2000, 4900);
    to(-2400, 5300);
    mark('road');

    // A staircase of windows: along, into the window, out, down a 45° step.
    const plots = (cfg.plots || []).slice(0, SITES.length).map((plot, j) => {
        const [x, y] = SITES[j];
        if (j) {
            const prev = SITES[j - 1];
            to(prev[0] - PLOT.w / 2 - 200, prev[1]);
            to(x + PLOT.w / 2 + 200, y);
        }
        to(x + PLOT.w / 2, y);
        mark(`p${j}in`);
        to(x - PLOT.w / 2, y);
        mark(`p${j}out`);
        return { ...plot, x, y, j };
    });

    to(FINAL.x, FINAL.y);
    mark('final');

    return { pts, marks, nodes, plots, length: len };
}

/** The shot list. Camera positions depend on the viewport, so it is rebuilt on resize. */
function buildKeys(world, stations, contentCount, view, rtl) {
    const { W, H, small } = view;
    const { marks, plots, nodes } = world;
    const side = rtl ? 1 : -1; // which way the picture is pushed to make room for the text
    const keys = [];
    const add = (u, x, y, zoom, o = {}) => keys.push({ u, x, y, lz: Math.log(zoom), rot: o.rot || 0, head: o.head ?? 0, scrim: o.scrim || 0, shade: o.shade ?? 0 });

    // Hero: the scene beside the headline (under it on a phone).
    const z0 = small ? Math.min((0.88 * W) / HERO.size.w, (0.36 * H) / HERO.size.h) : Math.min((0.5 * W) / HERO.size.w, (0.8 * H) / HERO.size.h);
    const hx = HERO.centre.x + (small ? 0 : (side * 0.22 * W) / z0);
    const hy = HERO.centre.y - (small ? (0.27 * H) / z0 : (0.04 * H) / z0);
    add(0, hx, hy, z0);
    add(0.8, hx, hy, z0);

    // The roadmap: the camera goes down the line. On wide screens the line
    // sits to one side, leaving the reading side to the pinned statement.
    const zr = small ? Math.min(0.5, W / 800) : Math.min(0.82, W / 1700);
    const shiftX = small ? 0 : (side * 0.12 * W) / zr;
    const shiftY = small ? (0.14 * H) / zr : 0;
    nodes.forEach((node, i) => {
        add(1.12 + i * 0.27, node.x + shiftX, node.y + shiftY, zr, { head: node.at + (small ? 180 : 260), shade: 1 });
    });

    // Where a window sits on screen while its card is being read.
    const frame = (plot) => {
        const h = PLOT.h + PLOT.bar;
        if (small) {
            const z = Math.min((0.9 * W) / PLOT.w, (0.34 * H) / h);
            return { x: plot.x, y: plot.y + (0.2 * H) / z, z };
        }
        const z = Math.min((0.52 * W) / PLOT.w, (0.6 * H) / h);
        return { x: plot.x + (side * 0.18 * W) / z, y: plot.y + (0.02 * H) / z, z };
    };

    const first = plots[0];
    const last = plots[plots.length - 1];
    stations.forEach((station, j) => {
        const i = station.index;
        const plot = plots[j];
        if (!plot) return;
        const fr = frame(plot);
        const inAt = marks[`p${j}in`];
        if (j === 0) {
            // Pull right back: all the projects at once, still drawings.
            const zo = Math.min((0.88 * W) / (first.x - last.x + PLOT.w + 900), (0.76 * H) / (last.y - first.y + PLOT.h + 1300));
            add(i, -2000 + shiftX * 0.5, 4800, zr * 0.8, { head: marks.road - 300, shade: 0.4 });
            add(i + 0.2, (first.x + last.x) / 2, (first.y + last.y) / 2 + (small ? (0.05 * H) / zo : 0), zo, { head: marks.road, rot: -0.03 * side });
        } else {
            const prev = plots[j - 1];
            const zm = Math.min((0.86 * W) / 4700, (0.8 * H) / 2500);
            add(i, (prev.x + plot.x) / 2, (prev.y + plot.y) / 2, zm, { head: lerp(marks[`p${j - 1}out`], inAt, 0.5), rot: (j % 2 ? 0.035 : -0.035) * side });
        }
        add(i + 0.46, fr.x, fr.y, fr.z, { head: inAt, shade: 1 });
        add(i + 0.88, fr.x, fr.y - 10 / fr.z, fr.z * 1.03, { head: inAt, shade: 1 });
        add(i + 0.94, fr.x, fr.y, fr.z * 0.92, { head: marks[`p${j}out`], shade: 0.4 });
    });

    // The final state, then a slow pull-back over the whole sheet while the
    // long sections are read.
    const lastStation = stations[stations.length - 1];
    const endU = lastStation ? lastStation.index + 1 : 2;
    const zx = small ? 0.3 : Math.min(0.5, (0.5 * W) / 1500);
    add(endU, FINAL.x + (small ? 0 : 500), FINAL.y, zx, { head: marks.final, scrim: 0.6 });
    const whole = { x: (HERO.centre.x + FINAL.x) / 2, y: (FINAL.y + HERO.centre.y) / 2, w: HERO.size.w / 2 - FINAL.x + 900, h: FINAL.y + 1700 };
    const zf = Math.min((0.9 * W) / whole.w, (0.74 * H) / whole.h);
    const count = Math.max(1, contentCount);
    for (let n = 1; n <= count; n++) {
        const t = smooth(n / count);
        add(endU + n, lerp(FINAL.x + 500, whole.x, t), lerp(FINAL.y, whole.y, t), Math.exp(lerp(Math.log(zx), Math.log(zf), t)), { head: marks.final, scrim: 0.86 });
    }
    return keys;
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
    const css = getComputedStyle(root);
    const ink = {
        bg: css.getPropertyValue('--board').trim() || '#0f172a',
        deep: css.getPropertyValue('--board-deep').trim() || '#16213b',
        line: css.getPropertyValue('--board-line').trim() || '#24abe3',
        text: '#f1f6fb',
    };

    const stations = [];
    sections.forEach((el, index) => {
        if (el.dataset.station) stations.push({ name: el.dataset.station, el, index, card: el.querySelector('[data-card]') });
    });
    const lastStation = stations[stations.length - 1];
    const contentCount = sections.length - (lastStation ? lastStation.index + 1 : 2);
    const world = buildWorld(cfg);
    const timeline = createTimeline(sections);
    const ideas = cfg.ideas || [];

    // ── Pictures: one screenshot per project (and its phone), nothing more ──
    // Asking for a picture and drawing it are separate: the hero's pictures
    // are asked for straight away, the stations' only once the page has
    // settled (or the reader is already on the way), so the first screen
    // never waits behind pictures that are four screens down.
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
    // Screens that cannot show more than 1600 pixels of a screenshot get the lighter copy.
    let lean = true;
    const coverOf = (plot) => (lean ? plot.small || plot.image : plot.image);
    const planOf = (plot) => (lean ? plot.planSmall || plot.plan : plot.plan);
    let warmed = false;
    const warm = () => {
        warmed = true;
        world.plots.forEach((p) => [coverOf(p), p.phone, planOf(p)].forEach(request));
    };
    ideas.forEach((idea) => request(idea.image));
    const settle = () => (window.requestIdleCallback ? window.requestIdleCallback(warm, { timeout: 2500 }) : setTimeout(warm, 1200));
    if (document.readyState === 'complete') settle();
    else window.addEventListener('load', settle, { once: true });

    // Rouaa: the swatches decide the tints of the three phones.
    const rouaa = stations.find((s) => s.name === 'rouaa');
    const swatches = rouaa ? [...rouaa.el.querySelectorAll('[data-tint]')] : [];
    const palette = swatches.map((s) => s.dataset.tint);
    let picked = 0;
    swatches.forEach((swatch, i) => swatch.addEventListener('click', () => { picked = i; }));

    const view = { W: 1, H: 1, dpr: 1, small: false };
    let keys = [];
    let path = {};
    const measure = () => {
        view.W = stage.clientWidth || window.innerWidth;
        view.H = window.innerHeight;
        view.small = view.W < 900;
        view.dpr = Math.min(window.devicePixelRatio || 1, 2);
        lean = view.W * view.dpr <= 2000;
        if (warmed) warm();
        canvas.width = Math.round(view.W * view.dpr);
        canvas.height = Math.round(view.H * view.dpr);
        keys = buildKeys(world, stations, contentCount, view, rtl);
        const us = keys.map((k) => k.u);
        path = Object.fromEntries(['x', 'y', 'lz', 'rot'].map((name) => [name, curve(us, keys.map((k) => k[name]))]));
    };
    measure();

    const state = { u: 0, target: 0, tail: 0, tailTarget: 0, px: 0, py: 0, lx: 0, ly: 0, sx: 0, sy: 0, pointerSeen: -1e9 };
    const cam = { x: 0, y: 0, zoom: 1, rot: 0, head: 0, scrim: 0, shade: 0 };
    const lamp = { x: 0.3, y: 0.5 };

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
    const toWorld = (sx, sy) => {
        const vx = (sx - view.W / 2) / cam.zoom;
        const vy = (sy - view.H / 2) / cam.zoom;
        const c = Math.cos(-cam.rot);
        const s = Math.sin(-cam.rot);
        return { x: cam.x + vx * c - vy * s, y: cam.y + vx * s + vy * c };
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

    function drawPen(head, time) {
        const r = clamp(13 * cam.zoom, 5.5, 16) / cam.zoom;
        const halo = ctx.createRadialGradient(head.x, head.y, 0, head.x, head.y, r * 7);
        halo.addColorStop(0, 'rgba(36, 171, 227, 0.55)');
        halo.addColorStop(1, 'rgba(36, 171, 227, 0)');
        ctx.fillStyle = halo;
        ctx.beginPath();
        ctx.arc(head.x, head.y, r * 7, 0, TAU);
        ctx.fill();
        ctx.fillStyle = ink.line;
        ctx.beginPath();
        ctx.arc(head.x, head.y, r * (1 + Math.sin(time * 3) * 0.08), 0, TAU);
        ctx.fill();
        ctx.fillStyle = '#ffffff';
        ctx.beginPath();
        ctx.arc(head.x, head.y, r * 0.36, 0, TAU);
        ctx.fill();
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

        // The port the route leaves by: a ring on the window's edge.
        ctx.beginPath();
        ctx.arc(port.x, port.y, 22, 0, TAU);
        ctx.fillStyle = ink.bg;
        ctx.fill();
        ctx.strokeStyle = ink.line;
        ctx.lineWidth = pen(5, 1.6, 5);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(port.x + 22, port.y);
        ctx.lineTo(win.x, port.y);
        ctx.stroke();
    }

    /** A UML note: a box with one corner folded, anchored by a dashed line. */
    function note(text, x, y, w, h, anchor, alpha) {
        const fold = 34;
        ctx.globalAlpha = alpha * 0.6;
        ctx.strokeStyle = ink.text;
        ctx.lineWidth = px(1.3);
        ctx.setLineDash([px(5), px(6)]);
        ctx.beginPath();
        ctx.moveTo(anchor.x, anchor.y);
        ctx.lineTo(anchor.x < x ? x : x + w, y + h / 2);
        ctx.stroke();
        ctx.setLineDash([]);
        ctx.globalAlpha = alpha;
        ctx.beginPath();
        ctx.moveTo(x, y);
        ctx.lineTo(x + w - fold, y);
        ctx.lineTo(x + w, y + fold);
        ctx.lineTo(x + w, y + h);
        ctx.lineTo(x, y + h);
        ctx.closePath();
        ctx.fillStyle = ink.deep;
        ctx.fill();
        ctx.lineWidth = pen(2.4, 1.2, 2.6);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(x + w - fold, y);
        ctx.lineTo(x + w - fold, y + fold);
        ctx.lineTo(x + w, y + fold);
        ctx.stroke();
        ctx.globalAlpha = 1;
        label(text, x + w / 2 - 6, y + h / 2 + 4, 33, { font: FONT.display, weight: 700, alpha });
    }

    function drawRoad() {
        // UML activity: each step is an action on the line, and beside it the
        // founder's question that the step answers.
        const reading = rtl ? -1 : 1; // the side the step's own text sits on
        world.nodes.forEach((node, i) => {
            const reached = smooth(cam.head, node.at - 60, node.at + 60);
            const next = world.nodes[i + 1];
            // A step steps back once the pen has moved on to the next one.
            const past = next ? 1 - smooth(cam.head, next.at - 260, next.at + 120) * 0.82 : 1;
            const shown = smooth(cam.head, node.at - 620, node.at - 160);
            if (shown <= 0.01) return;

            octagon(node.x, node.y, 44);
            ctx.fillStyle = reached > 0.5 ? ink.line : ink.bg;
            ctx.globalAlpha = shown;
            ctx.fill();
            ctx.strokeStyle = ink.line;
            ctx.lineJoin = 'miter';
            ctx.lineWidth = pen(4, 1.6, 4.5);
            ctx.stroke();
            ctx.globalAlpha = 1;
            ctx.direction = 'ltr';
            label(String(node.n), node.x, node.y + 3, 34, { font: FONT.mono, weight: 800, color: reached > 0.5 ? ink.bg : ink.line, alpha: shown });
            ctx.direction = rtl ? 'rtl' : 'ltr';

            const tx = node.x + reading * 96;
            const align = reading < 0 ? 'right' : 'left';
            const on = shown * (0.4 + reached * 0.6) * past;
            label(node.title, tx, node.y - 6, 46, { font: FONT.display, weight: 700, alpha: on, align });
            if (!view.small) {
                wrap(node.text, 470, 21, FONT.text, 400).slice(0, 3).forEach((line, n) => {
                    label(line, tx, node.y + 46 + n * 34, 21, { weight: 400, alpha: on * 0.78, align });
                });
            }
            if (node.question) {
                const w = 300;
                const h = 112;
                note(node.question, reading < 0 ? node.x + 120 : node.x - 120 - w, node.y - h / 2, w, h, { x: node.x - reading * 50, y: node.y }, shown * past);
            }
        });

        // UML decision on the jog: nothing is built before the client says yes.
        const shown = smooth(cam.head, world.marks.bend - 520, world.marks.bend - 120);
        const passed = smooth(cam.head, world.marks.bend - 20, world.marks.bend + 80);
        if (shown > 0.01) {
            ctx.beginPath();
            ctx.moveTo(BEND.x, BEND.y - 56);
            ctx.lineTo(BEND.x + 56, BEND.y);
            ctx.lineTo(BEND.x, BEND.y + 56);
            ctx.lineTo(BEND.x - 56, BEND.y);
            ctx.closePath();
            ctx.globalAlpha = shown;
            ctx.fillStyle = passed > 0.5 ? ink.line : ink.bg;
            ctx.fill();
            ctx.strokeStyle = ink.line;
            ctx.lineWidth = pen(4, 1.6, 4.5);
            ctx.stroke();
            ctx.globalAlpha = 1;
            label(cfg.labels?.approve, BEND.x - reading * 92, BEND.y - 2, 28, { weight: 600, alpha: shown * 0.95, align: reading < 0 ? 'left' : 'right', color: ink.line });
        }

        // UML actor at the top of the road: the founder, with the idea in hand.
        const who = smooth(state.u, 0.9, 1.1) * (1 - smooth(state.u, 1.3, 1.5));
        if (who > 0.01 && world.nodes.length) {
            const ax = -1500 - reading * 330;
            const ay = 880;
            ctx.strokeStyle = ink.text;
            ctx.globalAlpha = who * 0.9;
            ctx.lineWidth = pen(5, 1.6, 5);
            ctx.lineCap = 'round';
            ctx.beginPath();
            ctx.arc(ax, ay - 96, 34, 0, TAU);
            ctx.moveTo(ax, ay - 62);
            ctx.lineTo(ax, ay + 40);
            ctx.moveTo(ax - 62, ay - 22);
            ctx.lineTo(ax + 62, ay - 22);
            ctx.moveTo(ax, ay + 40);
            ctx.lineTo(ax - 50, ay + 124);
            ctx.moveTo(ax, ay + 40);
            ctx.lineTo(ax + 50, ay + 124);
            ctx.stroke();
            ctx.lineCap = 'butt';
            ctx.globalAlpha = 1;
            label(cfg.labels?.you, ax, ay + 178, 34, { font: FONT.display, weight: 700, alpha: who });
        }
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

    /** A phone: a tall cut box whose screen rises from the bottom as it is built. */
    function phoneFrame(x, y, w, h, built, image, tint = null) {
        lifted(built, () => {
            cutBox(x, y, w, h, 34);
            ctx.fill();
        });

        ctx.save();
        cutBox(x, y, w, h, 34);
        ctx.clip();
        if (built < 0.999) wireframe(x, y, w, h);
        if (built > 0.001 && image) {
            ctx.save();
            ctx.beginPath();
            ctx.rect(x, y + h * (1 - built), w, h * built);
            ctx.clip();
            const sh = Math.min(image.naturalHeight, image.naturalWidth * (h / w));
            ctx.drawImage(image, 0, 0, image.naturalWidth, sh, x, y, w, h);
            if (tint) {
                // Re-colour the site the way its own dashboard does: hue only.
                ctx.globalCompositeOperation = 'hue';
                ctx.fillStyle = tint;
                ctx.fillRect(x, y, w, h);
                ctx.globalCompositeOperation = 'source-over';
            }
            ctx.restore();
        }
        ctx.restore();
        ctx.strokeStyle = ink.line;
        ctx.lineJoin = 'miter';
        ctx.lineWidth = pen(4, 1.4, 4);
        cutBox(x, y, w, h, 34);
        ctx.stroke();
        if (built > 0.004 && built < 0.996) {
            ctx.strokeStyle = '#ffffff';
            ctx.lineWidth = pen(6, 2, 6);
            ctx.beginPath();
            ctx.moveTo(x - 20, y + h * (1 - built));
            ctx.lineTo(x + w + 20, y + h * (1 - built));
            ctx.stroke();
        }
    }

    let focus = 0; // 1 while the camera is standing at some station
    function drawPlot(plot, local) {
        const total = PLOT.h + PLOT.bar;
        const x = plot.x - PLOT.w / 2;
        const y = plot.y - total / 2;
        const built = local >= 1 ? 1 : smooth(local, 0.48, 0.76);
        if (!warmed) warm(); // already here before the page settled
        const shot = picture(coverOf(plot));

        // Where the line comes in: the logo's arrowhead. Where it leaves: a ring.
        ctx.fillStyle = ink.line;
        arrowhead(x + PLOT.w + 6, plot.y, -1, 0, 46);
        ctx.beginPath();
        ctx.arc(x - 34, plot.y, 22, 0, TAU);
        ctx.fillStyle = ink.bg;
        ctx.fill();
        ctx.strokeStyle = ink.line;
        ctx.lineWidth = pen(5, 1.6, 5);
        ctx.stroke();

        // The name above it, until the HTML card takes over at the stop.
        const titled = (local > 0 && local < 1 ? 1 - smooth(local, 0.28, 0.44) : 1) * (1 - focus);
        label(plot.name, plot.x, y - 96, 110, { font: FONT.display, weight: 800, alpha: titled });

        if (plot.scene === 'rouaa') {
            // Three phones, three tints of the same site.
            const screen = picture(plot.phone) || shot;
            const w = 400;
            const gap = (PLOT.w - w * 3) / 2;
            [0, 1, 2].forEach((n) => {
                const order = 2 - n;
                const tint = palette.length ? palette[(picked + [0, 2, 3][order]) % palette.length] : null;
                const rise = local >= 1 ? 1 : smooth(local, 0.46 + order * 0.08, 0.66 + order * 0.08);
                phoneFrame(x + n * (w + gap), y, w, total, rise, screen, tint);
            });
            return;
        }

        windowFrame({ x, y, w: PLOT.w, h: PLOT.h, bar: PLOT.bar, name: plot.name }, built, (bx, by, bw, bh) => {
            if (plot.scene === 'nour') {
                // A dark room and one lamp; by the end the whole site is lit.
                const dark = local >= 1 ? 1 : smooth(local, 0.4, 0.5);
                if (dark < 1) wireframe(bx, by, bw, bh);
                ctx.globalAlpha = dark;
                ctx.fillStyle = '#050403';
                ctx.fillRect(bx, by, bw, bh);
                ctx.globalAlpha = 1;
                if (shot && dark > 0) {
                    const open = smooth(local, 0.5, 0.7);
                    const flood = local >= 1 ? 1 : smooth(local, 0.78, 0.9);
                    const lx = bx + lamp.x * bw;
                    const ly = by + lamp.y * bh;
                    const r = 150 + open * 130 + flood * 2200;
                    ctx.globalAlpha = 0.1 * dark;
                    ctx.drawImage(shot, bx, by, bw, bh);
                    [[1.7, 0.22], [1.3, 0.5], [1, 1]].forEach(([k, alpha]) => {
                        ctx.save();
                        ctx.beginPath();
                        ctx.arc(lx, ly, r * k, 0, TAU);
                        ctx.clip();
                        ctx.globalAlpha = alpha * smooth(local, 0.44, 0.56);
                        ctx.drawImage(shot, bx, by, bw, bh);
                        ctx.restore();
                    });
                    ctx.globalAlpha = 1;
                }
                return;
            }

            const plan = picture(planOf(plot));
            if (plan) {
                // Its own blueprint, wiped into the real site from the side the pen came in.
                const cut = bx + bw - built * bw;
                ctx.drawImage(plan, bx, by, bw, bh);
                if (shot && built > 0.001) {
                    ctx.save();
                    ctx.beginPath();
                    ctx.rect(cut, by, bx + bw - cut, bh);
                    ctx.clip();
                    ctx.drawImage(shot, bx, by, bw, bh);
                    ctx.restore();
                }
                if (built > 0.004 && built < 0.996) {
                    ctx.strokeStyle = '#ffffff';
                    ctx.lineWidth = pen(6, 2, 6);
                    ctx.beginPath();
                    ctx.moveTo(cut, by);
                    ctx.lineTo(cut, by + bh);
                    ctx.stroke();
                }
                return;
            }

            if (built < 0.999) wireframe(bx, by, bw, bh);
            if (shot && built > 0.001) tiles(shot, bx, by, bw, bh, built, 1);
        });

        // Its phone, leaning on the corner the pen leaves by.
        if (plot.phone) {
            const rise = local >= 1 ? 1 : smooth(local, 0.6, 0.82);
            phoneFrame(x + 64, y + total - PHONE.h + 96, PHONE.w, PHONE.h, rise, picture(plot.phone));
        }
    }

    function drawFinal() {
        // UML final state: a ring with a filled dot. Launch.
        const reached = smooth(cam.head, world.marks.final - 400, world.marks.final);
        ctx.beginPath();
        ctx.arc(FINAL.x, FINAL.y, 120, 0, TAU);
        ctx.fillStyle = ink.bg;
        ctx.fill();
        ctx.strokeStyle = ink.line;
        ctx.lineWidth = pen(9, 2, 8);
        ctx.stroke();
        ctx.beginPath();
        ctx.arc(FINAL.x, FINAL.y, 66, 0, TAU);
        ctx.fillStyle = ink.line;
        ctx.globalAlpha = 0.2 + reached * 0.8;
        ctx.fill();
        ctx.globalAlpha = 1;
        label(cfg.labels?.launch, FINAL.x - 190, FINAL.y + 8, 150, { font: FONT.display, weight: 800, align: 'right', alpha: reached * (1 - cam.scrim) });
    }

    /** Screen space: a dashed UML realization from each built window to the card that specifies it. */
    function drawLeaders() {
        if (view.small) return;
        ctx.setTransform(view.dpr, 0, 0, view.dpr, 0, 0);
        ctx.strokeStyle = ink.line;
        ctx.lineWidth = 1.5;
        const top = stage.getBoundingClientRect().top;
        stations.forEach((station, j) => {
            const plot = world.plots[j];
            const local = state.u - station.index;
            if (!plot || !station.card || local < 0.5 || local > 0.95) return;
            const alpha = smooth(local, 0.58, 0.66) * (1 - smooth(local, 0.88, 0.94));
            if (alpha <= 0.01) return;
            const r = station.card.getBoundingClientRect();
            const cardX = rtl ? r.left : r.right;
            const cardY = r.top - top + 46;
            const edge = toScreen(plot.x + ((rtl ? 1 : -1) * PLOT.w) / 2, plot.y - 260);
            const midX = (edge.x + cardX) / 2;
            const d = rtl ? 1 : -1;
            ctx.globalAlpha = alpha * 0.9;
            ctx.setLineDash([6, 6]);
            ctx.beginPath();
            ctx.moveTo(edge.x, edge.y);
            ctx.lineTo(midX, edge.y);
            ctx.lineTo(midX, cardY);
            ctx.lineTo(cardX - d * 17, cardY);
            ctx.stroke();
            ctx.setLineDash([]);
            ctx.beginPath();
            ctx.moveTo(cardX - d, cardY);
            ctx.lineTo(cardX - d * 17, cardY - 9);
            ctx.lineTo(cardX - d * 17, cardY + 9);
            ctx.closePath();
            ctx.fillStyle = ink.bg;
            ctx.fill();
            ctx.stroke();
            ctx.globalAlpha = 1;
        });
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
        drawRoad();
        focus = stations.reduce((most, st) => Math.max(most, smooth(state.u - st.index, 0.3, 0.44) * (1 - smooth(state.u - st.index, 0.92, 1))), 0);
        world.plots.forEach((plot, j) => {
            const station = stations[j];
            const s = toScreen(plot.x, plot.y);
            const reach = (PLOT.w * cam.zoom) / 2 + 260;
            if (s.x < -reach || s.x > W + reach || s.y < -reach || s.y > H + reach) return;
            drawPlot(plot, station ? state.u - station.index : 0);
        });
        drawFinal();
        drawPen(head, time);
        drawLeaders();

        stage.style.setProperty('--scrim', cam.scrim.toFixed(3));
        stage.style.setProperty('--shade', cam.shade.toFixed(3));
        if (hud) {
            const n = (v) => String(Math.round(Math.abs(v))).padStart(5, '0');
            hud.textContent = `X ${n(cam.x)}  Y ${n(cam.y)}  ×${cam.zoom.toFixed(2)}`;
        }
    }

    const nour = world.plots.find((p) => p.scene === 'nour');
    const aimLamp = (time) => {
        if (!nour) return;
        let tx;
        let ty;
        if (performance.now() - state.pointerSeen > 2600) {
            tx = 0.5 + Math.cos(time * 0.5) * 0.3;
            ty = 0.5 + Math.sin(time * 0.8) * 0.22;
        } else {
            const p = toWorld(state.sx, state.sy);
            tx = clamp((p.x - (nour.x - PLOT.w / 2)) / PLOT.w, 0, 1);
            ty = clamp((p.y - (nour.y - PLOT.h / 2)) / PLOT.h, 0, 1);
        }
        lamp.x += (tx - lamp.x) * 0.09;
        lamp.y += (ty - lamp.y) * 0.09;
    };

    const readScroll = () => {
        const { u, tail } = timeline.read();
        state.target = u;
        state.tailTarget = tail;
    };
    window.addEventListener('scroll', readScroll, { passive: true });
    window.addEventListener('pointermove', (e) => {
        state.px = (e.clientX / window.innerWidth) * 2 - 1;
        state.py = (e.clientY / window.innerHeight) * 2 - 1;
        state.sx = e.clientX;
        state.sy = e.clientY;
        state.pointerSeen = performance.now();
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
        aimLamp(clock.t);
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
            aimLamp(time);
            draw(time);
        },
        yFor: (u) => timeline.yFor(u),
        length: keys[keys.length - 1].u,
        stations: stations.map((s) => ({ name: s.name, index: s.index })),
    };
    window.__journey = api;
    return api;
}
