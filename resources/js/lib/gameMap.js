// Pure geometry + generation helpers for the eligibility game map.
// No Vue dependency → unit-testable in isolation.

// ─── Seeded RNG (LCG) ─────────────────────────────────────────────────────────
export function mkRng(seed) {
    let s = (seed * 1664525 + 1013904223) & 0x7fffffff;

    return () => {
        s = (s * 1664525 + 1013904223) & 0x7fffffff;

        return s / 0x7fffffff;
    };
}

// ─── Map / path generation ────────────────────────────────────────────────────
// Path starts at y = vh/2 so that at progress=0 the map sits flush at the top of
// the viewport (mapTY ≈ 0). MAPW = 3 × vw absorbs horizontal camera movement
// without revealing empty edges. The path uses H/V segments only (no diagonals).
const COL_PCTS = [0.1, 0.24, 0.38, 0.55, 0.7, 0.86];

export function buildMap(count, vw, vh, rng) {
    const mapWidth = vw * 3;
    const OX = vw; // left offset for the centre third
    const PATH_START = Math.round(vh / 2);

    const colXs = COL_PCTS.map((p) => OX + Math.round(vw * p));

    const segs = [];
    const chkps = [];
    let x = OX + Math.round(vw / 2);
    let y = PATH_START;
    let colIdx = 2;
    let total = 0;

    function addSeg(x1, y1, x2, y2) {
        const len = Math.abs(x2 - x1) + Math.abs(y2 - y1);

        segs.push({ x1, y1, x2, y2, length: len });
        total += len;
    }

    const sx = x;
    const sy = y;

    // Opening vertical before first checkpoint
    addSeg(x, y, x, y + 220 + Math.round(rng() * 100));
    y = segs[segs.length - 1].y2;

    for (let i = 0; i < count; i++) {
        let nextCol;
        let tries = 0;

        do {
            nextCol = Math.floor(rng() * colXs.length);
            tries++;
        } while (nextCol === colIdx && tries < 20);

        // Horizontal → checkpoint at the end
        addSeg(x, y, colXs[nextCol], y);
        x = colXs[nextCol];
        colIdx = nextCol;
        chkps.push({ x, y, index: i, progress: total });

        // Vertical descent to next row
        addSeg(x, y, x, y + 380 + Math.round(rng() * 180));
        y = segs[segs.length - 1].y2;
    }

    // Closing tail
    addSeg(x, y, x, y + 280);
    y = segs[segs.length - 1].y2;

    return {
        segs,
        chkps,
        total,
        height: y + Math.round(vh * 0.6),
        mapWidth,
        startPt: { x: sx, y: sy },
        endPt: { x, y },
    };
}

// ─── Corner data ──────────────────────────────────────────────────────────────
// For each bend, stores the cumulative progress at the corner, the corner point,
// and the two bezier control points. The blend radius is clamped to half the
// shorter adjacent segment so neighbouring corners never overlap.
export function computeCorners(segs, blend) {
    const out = [];
    let acc = 0;

    for (let i = 0; i < segs.length - 1; i++) {
        const s = segs[i];
        const nx = segs[i + 1];

        acc += s.length;

        const b = Math.min(blend, s.length / 2, nx.length / 2);
        const dx1 = s.x2 !== s.x1 ? Math.sign(s.x2 - s.x1) : 0;
        const dy1 = s.y2 !== s.y1 ? Math.sign(s.y2 - s.y1) : 0;
        const dx2 = nx.x2 !== nx.x1 ? Math.sign(nx.x2 - nx.x1) : 0;
        const dy2 = nx.y2 !== nx.y1 ? Math.sign(nx.y2 - nx.y1) : 0;

        out.push({
            prog: acc,
            b,
            px: s.x2,
            py: s.y2,
            P0x: s.x2 - dx1 * b,
            P0y: s.y2 - dy1 * b,
            P3x: s.x2 + dx2 * b,
            P3y: s.y2 + dy2 * b,
        });
    }

    return out;
}

// ─── Smooth arc-length position ───────────────────────────────────────────────
// Outside a corner: linear interpolation along the H/V segment.
// Inside ±b of a corner: smoothstep-reparametrised quadratic bezier for an
// ease-in-out turn.
export function smoothPos(segs, corners, prog) {
    let best = null;
    let bestDist = Infinity;

    for (const c of corners) {
        const d = Math.abs(prog - c.prog);

        if (d < c.b && d < bestDist) {
            bestDist = d;
            best = c;
        }
    }

    if (best) {
        const d = prog - best.prog;
        const t = (d + best.b) / (2 * best.b);
        const st = t * t * (3 - 2 * t);
        const mt = 1 - st;

        return {
            x: Math.round(
                mt * mt * best.P0x + 2 * mt * st * best.px + st * st * best.P3x,
            ),
            y: Math.round(
                mt * mt * best.P0y + 2 * mt * st * best.py + st * st * best.P3y,
            ),
        };
    }

    let rem = prog;

    for (const s of segs) {
        if (rem <= s.length) {
            const t = s.length > 0 ? rem / s.length : 0;

            return {
                x: Math.round(s.x1 + (s.x2 - s.x1) * t),
                y: Math.round(s.y1 + (s.y2 - s.y1) * t),
            };
        }

        rem -= s.length;
    }

    const last = segs[segs.length - 1];

    return last ? { x: last.x2, y: last.y2 } : { x: 0, y: 0 };
}

// ─── SVG point helpers ────────────────────────────────────────────────────────
export function polyPts(segs) {
    if (!segs.length) {
        return '';
    }

    const pts = [`${segs[0].x1},${segs[0].y1}`];

    for (const s of segs) {
        pts.push(`${s.x2},${s.y2}`);
    }

    return pts.join(' ');
}

export function octPts(cx, cy, r, c) {
    return `${cx - r + c},${cy - r} ${cx + r - c},${cy - r} ${cx + r},${cy - r + c} ${cx + r},${cy + r - c} ${cx + r - c},${cy + r} ${cx - r + c},${cy + r} ${cx - r},${cy + r - c} ${cx - r},${cy - r + c}`;
}

// ─── Decoration scatter ───────────────────────────────────────────────────────
function nearPath(px, py, segs, clr) {
    for (const s of segs) {
        if (
            px >= Math.min(s.x1, s.x2) - clr &&
            px <= Math.max(s.x1, s.x2) + clr &&
            py >= Math.min(s.y1, s.y2) - clr &&
            py <= Math.max(s.y1, s.y2) + clr
        ) {
            return true;
        }
    }

    return false;
}

const DECO_TYPES = [
    { type: 'pine', w: 0.24 },
    { type: 'oak', w: 0.48 },
    { type: 'rock', w: 0.62 },
    { type: 'bush', w: 0.74 },
    { type: 'flower', w: 0.87 },
    { type: 'mushroom', w: 1 },
];

function pickType(rng) {
    const r = rng();

    return (
        DECO_TYPES.find((t) => r < t.w) || DECO_TYPES[DECO_TYPES.length - 1]
    ).type;
}

export function buildDecos(segs, mapWidth, height, seed) {
    const rng = mkRng(seed);
    const out = [];
    const STEP = 100;
    const CLR = 58;

    for (let y = 30; y < height - 80; y += STEP) {
        for (let x = 30; x < mapWidth - 30; x += STEP) {
            if (rng() <= 0.42) {
                continue;
            }

            // Jitter first, then test clearance at the real placement position
            const jx = Math.round(x + (rng() - 0.5) * 55);
            const jy = Math.round(y + (rng() - 0.5) * 55);

            if (!nearPath(jx, jy, segs, CLR)) {
                out.push({ x: jx, y: jy, type: pickType(rng) });
            }
        }
    }

    return out;
}

// ─── Canvas grass tile ────────────────────────────────────────────────────────
// Renders a seamless 32px pixel-art grass tile (speckles + blades) and returns a
// PNG data-URL. Deterministic. Pass `colors` to re-theme the terrain.
export function generateGrassTile(colors) {
    const c = Object.assign(
        {
            base: '#4a7c45',
            light: '#558d4f',
            dark: '#3d6b39',
            blade: '#6aa85f',
            shadow: '#356030',
        },
        colors,
    );

    const N = 16;
    const px = 2;
    const S = N * px;
    const canvas = document.createElement('canvas');

    canvas.width = S;
    canvas.height = S;

    const ctx = canvas.getContext('2d');
    const rng = mkRng(20260602);

    ctx.fillStyle = c.base;
    ctx.fillRect(0, 0, S, S);

    const dot = (gx, gy, color) => {
        ctx.fillStyle = color;
        ctx.fillRect(
            (((gx % N) + N) % N) * px,
            (((gy % N) + N) % N) * px,
            px,
            px,
        );
    };

    for (let i = 0; i < N * N; i++) {
        const r = rng();

        if (r < 0.14) {
            dot(i % N, Math.floor(i / N), c.light);
        } else if (r < 0.24) {
            dot(i % N, Math.floor(i / N), c.dark);
        }
    }

    for (let i = 0; i < 6; i++) {
        const gx = 1 + Math.floor(rng() * (N - 2));
        const gy = 1 + Math.floor(rng() * (N - 3));

        dot(gx, gy, c.blade);
        dot(gx, gy + 1, c.shadow);
    }

    return canvas.toDataURL('image/png');
}
