/**
 * Éditions du Trophée de la générosité et leurs vainqueurs (podium 1/2/3).
 *
 * Données statiques temporaires : à terme, elles seront fournies par le back-end
 * (modèle Eloquent + props Inertia). Garder cette structure pour faciliter le
 * branchement (mêmes clés : year, winners[{ rank, logo, name }]).
 */
export const editions = [
    {
        year: 2026,
        winners: [
            { rank: 1, logo: '/img/rolex.svg', name: 'Rolex' },
            { rank: 2, logo: '/img/migros.png', name: 'Migros' },
            { rank: 3, logo: '/img/nestle.png', name: 'Nestlé' },
        ],
    },
    {
        year: 2010,
        winners: [
            { rank: 1, logo: '/img/nestle.png', name: 'Nestlé' },
            { rank: 2, logo: '/img/rolex.svg', name: 'Rolex' },
            { rank: 3, logo: '/img/migros.png', name: 'Migros' },
        ],
    },
    {
        year: 2009,
        winners: [
            { rank: 1, logo: '/img/migros.png', name: 'Migros' },
            { rank: 2, logo: '/img/nestle.png', name: 'Nestlé' },
            { rank: 3, logo: '/img/rolex.svg', name: 'Rolex' },
        ],
    },
    {
        year: 2008,
        winners: [
            { rank: 1, logo: '/img/rolex.svg', name: 'Rolex' },
            { rank: 2, logo: '/img/migros.png', name: 'Migros' },
            { rank: 3, logo: '/img/nestle.png', name: 'Nestlé' },
        ],
    },
];
