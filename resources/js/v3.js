/**
 * v3.js — motion add-ons for v3 (loaded alongside app.js, which drives scroll
 * reveals, counters, tilt, magnetic, etc.). Adds: a choreographed on-load
 * entrance, image clip-reveals, and a soft cursor-follow glow.
 * Gated under html.js; reduced-motion + coarse-pointer aware.
 */

const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)');
const LOAD_STEP = 90;

document.addEventListener('DOMContentLoaded', () => {
    initLoad();
    initNetbg();

    // Reduced-motion OR a not-yet-visible tab (rAF/IO throttled): just show the
    // images, no clip-reveal. The visible, motion-OK path gets the full effect.
    if (reduced.matches || document.hidden) {
        document.querySelectorAll('[data-img-reveal]').forEach((el) => el.classList.add('in'));
        return;
    }
    initImgReveal();
    if (finePointer.matches) initCursorGlow();
});

/* ── Mouse-reactive starfield ────────────────────────────────────────────────
   A fixed, viewport-sized canvas of faint cyan-white "stars" that drift slowly
   and parallax-shift toward the pointer. Pure decoration: one rAF loop, density
   scales with viewport area, and it degrades to a single static frame under
   reduced motion. */
function initNetbg() {
    const host = document.getElementById('netbg');
    if (!host) return;

    const reduce = reduced.matches;
    const canvas = document.createElement('canvas');
    host.appendChild(canvas);
    const ctx = canvas.getContext('2d');

    let W = 0, H = 0, dpr = 1, stars = [];
    let mx = 0, my = 0, tx = 0, ty = 0; // pointer target + eased camera

    const resize = () => {
        dpr = Math.min(window.devicePixelRatio || 1, 2);
        W = window.innerWidth;
        H = window.innerHeight;
        canvas.width = W * dpr;
        canvas.height = H * dpr;
        canvas.style.width = W + 'px';
        canvas.style.height = H + 'px';
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

        // Denser field (~1 star per 5.5k px²), capped for performance.
        const count = Math.min(Math.round((W * H) / 5500), 340);
        stars = Array.from({ length: count }, () => ({
            x: Math.random() * W,
            y: Math.random() * H,
            z: Math.random() * 0.8 + 0.2, // depth → size + parallax strength
            r: Math.random() * 1.3 + 0.3,
            a: Math.random() * 0.5 + 0.2, // base alpha
            tw: Math.random() * Math.PI * 2, // twinkle phase
            vx: (Math.random() - 0.5) * 0.05,
            vy: (Math.random() - 0.5) * 0.05,
        }));
    };

    const frame = (t) => {
        ctx.clearRect(0, 0, W, H);
        tx += (mx - tx) * 0.08;
        ty += (my - ty) * 0.08;
        for (const s of stars) {
            if (!reduce) {
                s.x += s.vx;
                s.y += s.vy;
                if (s.x < 0) s.x += W; else if (s.x > W) s.x -= W;
                if (s.y < 0) s.y += H; else if (s.y > H) s.y -= H;
            }
            const px = s.x + tx * s.z * 110; // parallax toward the pointer (stronger)
            const py = s.y + ty * s.z * 110;
            const tw = reduce ? 1 : 0.6 + 0.4 * Math.sin(t * 0.001 + s.tw);
            ctx.beginPath();
            ctx.arc(px, py, s.r * s.z + 0.2, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(150, 210, 245, ${(s.a * tw).toFixed(3)})`;
            ctx.fill();
        }
        if (!reduce) requestAnimationFrame(frame);
    };

    const onResize = () => {
        resize();
        if (reduce) requestAnimationFrame(frame); // redraw the single static frame
    };

    window.addEventListener('pointermove', (e) => {
        mx = e.clientX / window.innerWidth - 0.5;
        my = e.clientY / window.innerHeight - 0.5;
    }, { passive: true });
    window.addEventListener('resize', onResize, { passive: true });

    resize();
    requestAnimationFrame(frame);
}

function initLoad() {
    document.querySelectorAll('[data-load]').forEach((group) => {
        group.querySelectorAll('[data-load-item]').forEach((el, i) => {
            el.style.setProperty('--ld', `${i * LOAD_STEP}ms`);
        });
        const reveal = () => group.classList.add('ready'); // idempotent
        if (reduced.matches || document.hidden) {
            reveal(); // no entrance animation needed when reduced or off-screen
            return;
        }
        requestAnimationFrame(() => requestAnimationFrame(reveal));
        // Safety net: rAF is paused in background tabs, so guarantee the reveal.
        setTimeout(reveal, 400);
    });
}

function initImgReveal() {
    const io = new IntersectionObserver(
        (entries, obs) => {
            entries.forEach((e) => {
                if (!e.isIntersecting) return;
                e.target.classList.add('in');
                obs.unobserve(e.target);
            });
        },
        { threshold: 0.2, rootMargin: '0px 0px -40px 0px' },
    );
    document.querySelectorAll('[data-img-reveal]').forEach((el) => io.observe(el));
}

/* Soft brand glow that eases toward the cursor (spring-like lerp). */
function initCursorGlow() {
    const glow = document.getElementById('cursor-glow');
    if (!glow) return;

    let tx = window.innerWidth / 2;
    let ty = window.innerHeight / 2;
    let x = tx;
    let y = ty;
    let visible = false;
    let raf = null;

    const tick = () => {
        x += (tx - x) * 0.12;
        y += (ty - y) * 0.12;
        glow.style.transform = `translate(${x}px, ${y}px)`;
        if (Math.abs(tx - x) > 0.5 || Math.abs(ty - y) > 0.5) {
            raf = requestAnimationFrame(tick);
        } else {
            raf = null;
        }
    };

    window.addEventListener('pointermove', (e) => {
        if (e.pointerType !== 'mouse') return;
        tx = e.clientX;
        ty = e.clientY;
        if (!visible) {
            visible = true;
            glow.style.opacity = '1';
        }
        if (!raf) raf = requestAnimationFrame(tick);
    }, { passive: true });

    document.addEventListener('mouseleave', () => {
        visible = false;
        glow.style.opacity = '0';
    });
}
