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

// ─── Arc-length position (linear interpolation along H/V segments) ────────────
export function smoothPos(segs, prog) {
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

// True if the axis-aligned box [x0,y0]-[x1,y1] overlaps any path segment (padded
// by clr). Used with a sprite's full silhouette so a tall billboard never paints
// across the road (where Pochy walks).
function rectHitsPath(x0, y0, x1, y1, segs, clr) {
    for (const s of segs) {
        const sx0 = Math.min(s.x1, s.x2) - clr;
        const sx1 = Math.max(s.x1, s.x2) + clr;
        const sy0 = Math.min(s.y1, s.y2) - clr;
        const sy1 = Math.max(s.y1, s.y2) + clr;

        if (x0 <= sx1 && x1 >= sx0 && y0 <= sy1 && y1 >= sy0) {
            return true;
        }
    }

    return false;
}

// A building sprite is tall and drawn upward from its base; check its whole body
// box against the path so its roof never overlaps the road / Pochy.
function buildingHitsPath(x, y, scale, segs, clr) {
    const halfW = 28 * scale;

    return rectHitsPath(
        x - halfW,
        y + 48 - 90 * scale,
        x + halfW,
        y + 46,
        segs,
        clr,
    );
}

// Render scale per sprite (1 = native 64×92 box). Buildings and themed shops are
// drawn large so the scene reads as a real city; street furniture stays modest.
const SCALE = {
    'building-a': 1.85,
    'building-b': 2.1,
    house: 1.6,
    kiosk: 1.3,
    streetlamp: 1.2,
    bench: 1.15,
    hydrant: 1.05,
    planter: 1.2,
    'trash-bin': 1.05,
    // Clins d'œil thématiques (repères)
    noticeboard: 1.6,
    fountain: 1.8,
    'pharmacy-shop': 2.0,
    'travel-shop': 2.0,
    'market-stall': 1.8,
    'hospital-building': 2.3,
    'dentist-shop': 2.0,
    'park-plot': 2.1,
    'tattoo-shop': 2.0,
    clinic: 2.0,
    // Végétation (couvert organique du parc)
    oak: 1.35,
    pine: 1.35,
    bush: 1.05,
    flower: 0.95,
    rock: 0.9,
    mushroom: 0.85,
    // Sur le lac (Léman)
    sailboat: 1.45,
    'jet-deau': 3.3,
    // Repères genevois (paysage de droite / nature)
    'train-station': 2.7,
    stadium: 2.9,
    'un-building': 2.7,
    // Drapeaux
    'flag-swiss': 1.3,
    'flag-geneva': 1.3,
    // En vol
    plane: 1.7,
    eagle: 1.1,
    // Animaux
    dog: 1.0,
    cat: 0.9,
    bird: 0.7,
};

// Themed landmark planted next to the matching checkpoint — a nod ("clin d'œil")
// to each quiz step. Keyed by the basename of the checkpoint's thematic icon.
const THEME_DECO = {
    'question-mark': 'noticeboard',
    heart: 'fountain',
    pharmacy: 'pharmacy-shop',
    airplane: 'travel-shop',
    fruits: 'market-stall',
    hospital: 'hospital-building',
    'tooth-mask': 'dentist-shop',
    'tick-trees': 'park-plot',
    tattoo: 'tattoo-shop',
    syringe: 'clinic',
};

function iconBasename(url) {
    if (!url) {
        return '';
    }

    return (String(url).split('/').pop() || '').replace(/\.svg$/, '');
}

function nearAny(x, y, items, clr) {
    for (const it of items) {
        if (Math.abs(x - it.x) < clr && Math.abs(y - it.y) < clr) {
            return true;
        }
    }

    return false;
}

function deco(x, y, type) {
    return { x, y, type, scale: SCALE[type] || 1 };
}

// True if (x,y) falls inside any rect (optionally padded). Used to keep buildings
// off the park lawns so a tower never lands in the middle of a green space.
function nearRects(x, y, rects, pad) {
    for (const r of rects) {
        if (
            x > r.x - pad &&
            x < r.x + r.w + pad &&
            y > r.y - pad &&
            y < r.y + r.h + pad
        ) {
            return true;
        }
    }

    return false;
}

// Place one themed landmark beside each checkpoint. Tries left/right at growing
// offsets and keeps the first spot clear of the path and inside the map bounds.
// Deterministic (offset side derived from the seed + checkpoint index).
function buildLandmarks(segs, mapWidth, seed, checkpoints, icons, water) {
    const out = [];
    const CLR = 96;
    const OFFSETS = [150, 188, 226];

    for (const cp of checkpoints) {
        const type = THEME_DECO[iconBasename(icons[cp.index])];

        if (!type) {
            continue;
        }

        const sideFirst = mkRng(seed + cp.index * 131)() < 0.5 ? -1 : 1;
        const sides = [sideFirst, -sideFirst];
        let placed = null;

        for (const off of OFFSETS) {
            for (const s of sides) {
                const x = cp.x + s * off;
                const y = cp.y + 6;

                if (x < 90 || x > mapWidth - 90) {
                    continue;
                }

                if (
                    !nearPath(x, y, segs, CLR) &&
                    !nearRects(x, y, water, 24) &&
                    !nearAny(x, y, out, 180)
                ) {
                    placed = { x, y };
                    break;
                }
            }

            if (placed) {
                break;
            }
        }

        if (placed) {
            out.push(deco(placed.x, placed.y, type));
        }
    }

    return out;
}

const TREES = ['oak', 'pine'];
const SHRUBS = ['bush', 'rock', 'mushroom'];

// Try to drop a sprite, skipping if it lands on the path, in the water, or on top
// of another already-placed item.
function tryPlace(out, x, y, type, segs, water, gap, pathClr) {
    if (
        nearPath(x, y, segs, pathClr) ||
        nearRects(x, y, water, 14) ||
        nearAny(x, y, out, gap)
    ) {
        return;
    }

    out.push(deco(x, y, type));
}

// The lush, organic cover of the park: a grid of cluster centres, each spawning a
// tight grove of trees, a flower bed, or a few shrubs/rocks — so the lawn reads
// full and natural (JRPG-overworld style) instead of dotted with lone props.
function scatterVegetation(segs, mapWidth, height, seed, water, avoid) {
    const rng = mkRng(seed + 333);
    const out = [];
    const STEP = 166;

    for (let gy = 30; gy < height - 70; gy += STEP) {
        for (let gx = 30; gx < mapWidth - 30; gx += STEP) {
            const cx = Math.round(gx + (rng() - 0.5) * 90);
            const cy = Math.round(gy + (rng() - 0.5) * 90);

            if (
                nearPath(cx, cy, segs, 52) ||
                nearRects(cx, cy, water, 12) ||
                nearAny(cx, cy, avoid, 104)
            ) {
                continue;
            }

            const roll = rng();

            if (roll < 0.48) {
                // Bosquet : touffe d'arbres serrée
                const n = 2 + Math.floor(rng() * 4);

                for (let i = 0; i < n; i++) {
                    tryPlace(
                        out,
                        Math.round(cx + (rng() - 0.5) * 66),
                        Math.round(cy + (rng() - 0.5) * 52),
                        TREES[rng() < 0.5 ? 0 : 1],
                        segs,
                        water,
                        30,
                        44,
                    );
                }
            } else if (roll < 0.8) {
                // Parterre de fleurs : petite grille colorée
                const cols = 2 + Math.floor(rng() * 2);
                const rows = 2 + Math.floor(rng() * 2);

                for (let r = 0; r < rows; r++) {
                    for (let cc = 0; cc < cols; cc++) {
                        tryPlace(
                            out,
                            Math.round(
                                cx + (cc - cols / 2) * 22 + (rng() - 0.5) * 8,
                            ),
                            Math.round(
                                cy + (r - rows / 2) * 20 + (rng() - 0.5) * 8,
                            ),
                            'flower',
                            segs,
                            water,
                            13,
                            38,
                        );
                    }
                }
            } else {
                // Buissons / rochers
                const n = 1 + Math.floor(rng() * 3);

                for (let i = 0; i < n; i++) {
                    tryPlace(
                        out,
                        Math.round(cx + (rng() - 0.5) * 52),
                        Math.round(cy + (rng() - 0.5) * 42),
                        SHRUBS[Math.floor(rng() * SHRUBS.length)],
                        segs,
                        water,
                        24,
                        42,
                    );
                }
            }
        }
    }

    return out;
}

// A sparse handful of Geneva buildings (houses, immeubles, stands) set on the
// grass beside the path — the town living among the parks. Kept light so the
// greenery dominates, à la overworld.
function scatterTown(segs, mapWidth, seed, water, avoid) {
    const rng = mkRng(seed + 222);
    const out = [];
    const TYPES = [
        'house',
        'house',
        'building-a',
        'building-b',
        'kiosk',
        'market-stall',
    ];
    const OFF = 128;

    for (const s of segs) {
        const horiz = s.y1 === s.y2;
        const len = s.length;

        if (len < 210) {
            continue;
        }

        for (let d = 100; d < len - 100; d += 250) {
            if (rng() < 0.42) {
                continue;
            }

            const t = d / len;
            const px = Math.round(s.x1 + (s.x2 - s.x1) * t);
            const py = Math.round(s.y1 + (s.y2 - s.y1) * t);
            const side = rng() < 0.5 ? -1 : 1;
            const x = horiz ? px : px + side * OFF;
            const y = horiz ? py - OFF : py;
            const type = TYPES[Math.floor(rng() * TYPES.length)];

            if (x < 40 || x > mapWidth - 40) {
                continue;
            }

            if (
                buildingHitsPath(x, y, SCALE[type], segs, 30) ||
                nearRects(x, y, water, 18) ||
                nearAny(x, y, out, 150) ||
                nearAny(x, y, avoid, 140)
            ) {
                continue;
            }

            out.push(deco(x, y, type));
        }
    }

    return out;
}

// Geneva touch on the water: the iconic Jet d'eau plus a few sailboats, scattered
// across the lake and kept clear of the shore.
function buildLakeProps(water, seed) {
    const lake = water[0];

    if (!lake) {
        return [];
    }

    const rng = mkRng(seed + 611);
    const out = [];
    const x0 = 50;
    const x1 = lake.innerX || lake.w - 60;

    if (x1 <= x0) {
        return out;
    }

    // Jet d'eau planted near the shore AND at the y where the path hugs the lake,
    // so it's on-screen exactly when the Léman fills the viewport.
    const jetY = Math.round(
        Math.max(260, Math.min(lake.nearY || lake.h * 0.3, lake.h - 320)),
    );

    out.push(deco(Math.round(Math.max(x0, x1 - 40)), jetY, 'jet-deau'));

    const STEP = 480;

    for (let y = 150; y < lake.h - 130; y += STEP) {
        if (rng() < 0.32) {
            continue;
        }

        const bx = Math.round(x0 + rng() * (x1 - x0));
        const by = Math.round(y + (rng() - 0.5) * 200);

        if (!nearAny(bx, by, out, 120)) {
            out.push(deco(bx, by, 'sailboat'));
        }
    }

    return out;
}

// Pack a paved district (un "quartier") with rows of buildings, leaving the street
// (path) and the railway clear — so each district reads as a coherent block.
function fillDistricts(districts, segs, seed, avoid, rails) {
    const rng = mkRng(seed + 444);
    const out = [];
    const STEP = 102;

    for (const d of districts) {
        for (let y = d.y + 56; y < d.y + d.h - 44; y += STEP) {
            for (let x = d.x + 50; x < d.x + d.w - 44; x += STEP) {
                const jx = Math.round(x + (rng() - 0.5) * 26);
                const jy = Math.round(y + (rng() - 0.5) * 26);
                const r = rng();
                const type =
                    r < 0.5
                        ? 'building-a'
                        : r < 0.82
                          ? 'building-b'
                          : 'house';

                if (
                    buildingHitsPath(jx, jy, SCALE[type], segs, 34) ||
                    nearRects(jx, jy, rails, 40) ||
                    nearAny(jx, jy, out, 96) ||
                    nearAny(jx, jy, avoid, 110)
                ) {
                    continue;
                }

                out.push(deco(jx, jy, type));
            }
        }
    }

    return out;
}

// A few animals (dogs, cats, pigeons) wandering the scene — sparse, off the water,
// and not stacked on anything else.
function scatterAnimals(segs, mapWidth, height, seed, water, avoid) {
    const rng = mkRng(seed + 888);
    const out = [];
    const KINDS = ['dog', 'cat', 'bird', 'bird'];
    const STEP = 500;

    for (let y = 120; y < height - 100; y += STEP) {
        for (let x = 80; x < mapWidth - 80; x += STEP) {
            if (rng() < 0.45) {
                continue;
            }

            const jx = Math.round(x + (rng() - 0.5) * 220);
            const jy = Math.round(y + (rng() - 0.5) * 220);

            if (
                nearRects(jx, jy, water, 8) ||
                nearPath(jx, jy, segs, 22) ||
                nearAny(jx, jy, avoid, 64)
            ) {
                continue;
            }

            out.push(deco(jx, jy, KINDS[Math.floor(rng() * KINDS.length)]));
        }
    }

    return out;
}

// CFF station planted on the LEFT of the railway, well clear of the tracks.
function buildRightLandmarks(rail, height) {
    if (!rail) {
        return [];
    }

    return [deco(rail.cx - 150, Math.round(height * 0.3), 'train-station')];
}

// Drop a big landmark on the GRASS right beside the path (so it's actually seen),
// trying a few offsets on either side and skipping the path, water, districts and
// rails. Returns null if no clear spot is found.
function placeBesidePath(segs, mapWidth, y, type, blockers) {
    const scale = SCALE[type];

    for (const off of [165, 205, 250, -165, -205, -250]) {
        const x = pathXAt(segs, y) + off;

        if (x < 70 || x > mapWidth - 70) {
            continue;
        }

        if (
            buildingHitsPath(x, y, scale, segs, 40) ||
            nearRects(x, y, blockers, 30)
        ) {
            continue;
        }

        return deco(x, y, type);
    }

    return null;
}

// Servette stadium and the UN (ONU) building, set in the nature beside the path at
// visible height bands.
function buildNatureLandmarks(segs, mapWidth, height, blockers) {
    const out = [];
    const stadium = placeBesidePath(
        segs,
        mapWidth,
        Math.round(height * 0.45),
        'stadium',
        blockers,
    );
    const un = placeBesidePath(
        segs,
        mapWidth,
        Math.round(height * 0.8),
        'un-building',
        blockers,
    );

    if (stadium) {
        out.push(stadium);
    }

    if (un) {
        out.push(un);
    }

    // Paire de drapeaux (Suisse + Genève) plantés ensemble près du chemin.
    const flag = placeBesidePath(
        segs,
        mapWidth,
        Math.round(height * 0.18),
        'flag-swiss',
        blockers,
    );

    if (flag) {
        const px = pathXAt(segs, flag.y);
        const outward = flag.x >= px ? 38 : -38; // 2e mât plus loin du chemin

        out.push(flag);
        out.push(deco(flag.x + outward, flag.y, 'flag-geneva'));
    }

    return out;
}

// Sparse airborne sprites flying over the scene (planes taking off, eagles in the
// nature). Drawn on top of the ground decor so they read as flying.
function buildFlyers(mapWidth, height, seed, avoid) {
    const rng = mkRng(seed + 999);
    const out = [];

    // Planes — rare, taking off across the sky.
    for (let y = 240; y < height - 240; y += 1700) {
        if (rng() < 0.4) {
            continue;
        }

        const jx = Math.round(240 + rng() * (mapWidth - 480));
        const jy = Math.round(y + (rng() - 0.5) * 700);

        if (!nearAny(jx, jy, out, 200) && !nearAny(jx, jy, avoid, 70)) {
            out.push(deco(jx, jy, 'plane'));
        }
    }

    // Eagles — a few soaring over the nature.
    for (let y = 200; y < height - 160; y += 760) {
        if (rng() < 0.5) {
            continue;
        }

        const jx = Math.round(180 + rng() * (mapWidth - 360));
        const jy = Math.round(y + (rng() - 0.5) * 420);

        if (!nearAny(jx, jy, out, 150)) {
            out.push(deco(jx, jy, 'eagle'));
        }
    }

    return out;
}

export function buildDecos(
    segs,
    mapWidth,
    height,
    seed,
    checkpoints = [],
    icons = [],
    zones = [],
) {
    const water = zones.filter((z) => z.kind === 'lake');
    const districts = zones.filter((z) => z.kind === 'district');
    const rail = zones.find((z) => z.kind === 'rail');
    const solid = [...water, ...districts, ...(rail ? [rail] : [])];

    const railList = rail ? [rail] : [];
    const landmarks = buildLandmarks(
        segs,
        mapWidth,
        seed,
        checkpoints,
        icons,
        water,
    );
    const rightLandmarks = buildRightLandmarks(rail, height);
    const natureLandmarks = buildNatureLandmarks(segs, mapWidth, height, solid);
    const bigLandmarks = [...rightLandmarks, ...natureLandmarks];
    const districtBuildings = fillDistricts(
        districts,
        segs,
        seed,
        [...landmarks, ...bigLandmarks],
        railList,
    );
    const builtAvoid = [...landmarks, ...bigLandmarks, ...districtBuildings];
    const town = scatterTown(segs, mapWidth, seed, solid, builtAvoid);
    const lakeProps = buildLakeProps(water, seed);
    const veg = scatterVegetation(segs, mapWidth, height, seed, solid, [
        ...builtAvoid,
        ...town,
    ]);
    const animals = scatterAnimals(segs, mapWidth, height, seed, water, [
        ...builtAvoid,
        ...town,
        ...veg,
    ]);
    const flyers = buildFlyers(mapWidth, height, seed, builtAvoid);

    // Paint back-to-front so closer sprites overlap the ones behind them, then put
    // the flyers (planes, eagles) on top — they're in the sky.
    const ground = [
        ...lakeProps,
        ...districtBuildings,
        ...bigLandmarks,
        ...landmarks,
        ...town,
        ...veg,
        ...animals,
    ].sort((a, b) => a.y - b.y);

    return [...ground, ...flyers];
}

// ─── Lake ground zone (le Léman, berge organique) ─────────────────────────────
// The path lives in the central third of the map, so the whole left band is free:
// we fill it with the Léman as an ORGANIC water body — full height on the left
// with a wavy right shoreline (never touching the road). Returns:
//   - `points`  : closed polygon for the water fill,
//   - `shore`   : the wavy edge polyline (drawn as a sand/dirt shoreline),
//   - `ripples` : light dashes on the water,
//   - x/y/w/h   : bounding box used to keep decorations off the water,
//   - `innerX`  : an x that is guaranteed to be in open water (for boats).
// Approximate x of the path at a given y (the vertical segment spanning it, else
// the nearest segment). Used to drop districts just BESIDE the street.
function pathXAt(segs, y) {
    let best = null;
    let bestDist = Infinity;

    for (const s of segs) {
        const lo = Math.min(s.y1, s.y2);
        const hi = Math.max(s.y1, s.y2);

        if (y >= lo && y <= hi) {
            return s.x1 === s.x2 ? s.x1 : Math.round((s.x1 + s.x2) / 2);
        }

        const d = Math.min(Math.abs(y - lo), Math.abs(y - hi));

        if (d < bestDist) {
            bestDist = d;
            best = s;
        }
    }

    return best ? best.x1 : 0;
}

export function buildZones(mapWidth, height, segs, seed) {
    if (!segs.length) {
        return [];
    }

    const rng = mkRng(seed + 777);

    let minX = mapWidth;
    let nearY = Math.round(height / 2); // y where the path hugs the lake the most

    for (const s of segs) {
        if (s.x1 < minX) {
            minX = s.x1;
            nearY = s.y1;
        }

        if (s.x2 < minX) {
            minX = s.x2;
            nearY = s.y2;
        }
    }

    const edge = Math.max(150, Math.round(minX - 70));
    const shorePts = [];
    let innerX = edge;

    for (let y = 0; y <= height; y += 70) {
        const x = Math.round(edge - 22 - rng() * 48); // edge-70 .. edge-22
        innerX = Math.min(innerX, x);
        shorePts.push([x, y]);
    }

    const shore = shorePts.map((p) => p.join(',')).join(' ');
    const polygon = `0,0 ${shore} 0,${height}`;

    const ripples = [];
    const rippleCount = Math.min(240, Math.round((edge * height) / 24000));

    for (let i = 0; i < rippleCount; i++) {
        ripples.push({
            dx: Math.round(20 + rng() * (innerX - 40)),
            dy: Math.round(10 + rng() * (height - 20)),
        });
    }

    const out = [
        {
            kind: 'lake',
            x: 0,
            y: 0,
            w: edge,
            h: height,
            innerX: Math.max(60, innerX - 24),
            nearY,
            points: polygon,
            shore,
            ripples,
        },
    ];

    // Two paved districts ("quartiers") set BESIDE the street (not across it), each
    // at its own height band, so the city has a couple of coherent built blocks.
    const dw = 640;
    const dh = 380;
    const gap = 104;

    for (const frac of [0.3, 0.68]) {
        const cy = Math.round(height * frac);
        const px = pathXAt(segs, cy);

        // Prefer the right of the street; fall back to the left if there's no room.
        let dx = px + gap;

        if (dx + dw > mapWidth - 24) {
            dx = px - gap - dw;
        }

        dx = Math.max(edge + 24, Math.min(dx, mapWidth - dw - 24));

        const dy = Math.max(20, Math.min(cy - dh / 2, height - dh - 20));

        out.push({ kind: 'district', x: dx, y: dy, w: dw, h: dh });
    }

    // CFF railway: a straight track running the full height on the far-right of the
    // landscape (right of the path), with the station planted beside it.
    let maxX = 0;

    for (const s of segs) {
        maxX = Math.max(maxX, s.x1, s.x2);
    }

    const railCx = Math.min(mapWidth - 120, Math.round(maxX + 230));

    if (railCx > maxX + 140) {
        out.push({
            kind: 'rail',
            x: railCx - 22, // bbox for decoration avoidance
            y: 0,
            w: 44,
            h: height,
            cx: railCx,
        });
    }

    return out;
}

// ─── Canvas ground tile ───────────────────────────────────────────────────────
// Renders a seamless 32px pixel-art lawn tile (parkland grass: speckles + the odd
// blade tuft) and returns a PNG data-URL. Deterministic. Pass `colors` to re-theme
// the terrain. Bright, lush green — the JRPG-overworld base of a Geneva park.
export function generateGroundTile(colors) {
    const c = Object.assign(
        {
            base: '#6aae3f', // lush parkland green
            light: '#79bd4c', // lighter speckle
            dark: '#579a34', // darker speckle
            blade: '#8cce5c', // bright grass blade
            shadow: '#4a8a2c', // blade shadow
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

    // A few little grass blades (blade + shadow underneath) for texture.
    for (let i = 0; i < 5; i++) {
        const gx = 1 + Math.floor(rng() * (N - 2));
        const gy = 1 + Math.floor(rng() * (N - 3));

        dot(gx, gy, c.blade);
        dot(gx, gy + 1, c.shadow);
    }

    return canvas.toDataURL('image/png');
}

// Seamless 32px asphalt tile — same speckled treatment as the grass (so the paved
// districts read textured, not flat) but in greys, with the odd darker crack.
export function generateAsphaltTile(colors) {
    const c = Object.assign(
        {
            base: '#7c7e83',
            light: '#8a8c91',
            dark: '#6d6f74',
            crack: '#5c5e63',
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
    const rng = mkRng(20260714);

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

        if (r < 0.16) {
            dot(i % N, Math.floor(i / N), c.light);
        } else if (r < 0.3) {
            dot(i % N, Math.floor(i / N), c.dark);
        }
    }

    // A couple of short cracks for grit.
    for (let i = 0; i < 3; i++) {
        const gx = 2 + Math.floor(rng() * (N - 4));
        const gy = 2 + Math.floor(rng() * (N - 4));

        dot(gx, gy, c.crack);
        dot(gx + 1, gy, c.crack);
    }

    return canvas.toDataURL('image/png');
}
