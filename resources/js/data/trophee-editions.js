/**
 * Éditions du Trophée de la générosité — podium (1/2/3) et prix d'honneur.
 *
 * Données statiques temporaires : à terme, elles seront fournies par le back-end
 * (modèle Eloquent + props Inertia). Conserver cette structure pour faciliter le
 * branchement (mêmes clés : year, winners, specialPrizes).
 */
export const editions = [
    {
        year: 2026,
        winners: [
            { rank: 1, logo: '/img/rolex.svg', name: 'Rolex' },
            { rank: 2, logo: '/img/migros.png', name: 'Migros' },
            { rank: 3, logo: '/img/nestle.png', name: 'Nestlé' },
        ],
        specialPrizes: [
            {
                id: 'grand_prix',
                nominations: [
                    { logo: '/img/rolex.svg', name: 'Rolex', isWinner: true },
                    {
                        logo: '/img/migros.png',
                        name: 'Migros',
                        isWinner: false,
                    },
                    {
                        logo: '/img/nestle.png',
                        name: 'Nestlé',
                        isWinner: false,
                    },
                ],
            },
            {
                id: 'progression',
                nominations: [
                    { logo: '/img/migros.png', name: 'Migros', isWinner: true },
                    {
                        logo: '/img/nestle.png',
                        name: 'Nestlé',
                        isWinner: false,
                    },
                    { logo: '/img/rolex.svg', name: 'Rolex', isWinner: false },
                ],
            },
            {
                id: 'fidelite',
                nominations: [
                    { logo: '/img/nestle.png', name: 'Nestlé', isWinner: true },
                    { logo: '/img/rolex.svg', name: 'Rolex', isWinner: false },
                    {
                        logo: '/img/migros.png',
                        name: 'Migros',
                        isWinner: false,
                    },
                ],
            },
            {
                id: 'coup_de_coeur',
                nominations: [
                    { logo: '/img/migros.png', name: 'Migros', isWinner: true },
                    {
                        logo: '/img/nestle.png',
                        name: 'Nestlé',
                        isWinner: false,
                    },
                    { logo: '/img/rolex.svg', name: 'Rolex', isWinner: false },
                ],
            },
        ],
    },
    {
        year: 2010,
        winners: [
            { rank: 1, logo: '/img/nestle.png', name: 'Nestlé' },
            { rank: 2, logo: '/img/rolex.svg', name: 'Rolex' },
            { rank: 3, logo: '/img/migros.png', name: 'Migros' },
        ],
        specialPrizes: [
            {
                id: 'grand_prix',
                nominations: [
                    { logo: '/img/nestle.png', name: 'Nestlé', isWinner: true },
                    { logo: '/img/rolex.svg', name: 'Rolex', isWinner: false },
                    {
                        logo: '/img/migros.png',
                        name: 'Migros',
                        isWinner: false,
                    },
                ],
            },
            {
                id: 'progression',
                nominations: [
                    { logo: '/img/rolex.svg', name: 'Rolex', isWinner: true },
                    {
                        logo: '/img/migros.png',
                        name: 'Migros',
                        isWinner: false,
                    },
                    {
                        logo: '/img/nestle.png',
                        name: 'Nestlé',
                        isWinner: false,
                    },
                ],
            },
            {
                id: 'fidelite',
                nominations: [
                    { logo: '/img/migros.png', name: 'Migros', isWinner: true },
                    {
                        logo: '/img/nestle.png',
                        name: 'Nestlé',
                        isWinner: false,
                    },
                    { logo: '/img/rolex.svg', name: 'Rolex', isWinner: false },
                ],
            },
            {
                id: 'coup_de_coeur',
                nominations: [
                    { logo: '/img/nestle.png', name: 'Nestlé', isWinner: true },
                    { logo: '/img/rolex.svg', name: 'Rolex', isWinner: false },
                    {
                        logo: '/img/migros.png',
                        name: 'Migros',
                        isWinner: false,
                    },
                ],
            },
        ],
    },
    {
        year: 2009,
        winners: [
            { rank: 1, logo: '/img/migros.png', name: 'Migros' },
            { rank: 2, logo: '/img/nestle.png', name: 'Nestlé' },
            { rank: 3, logo: '/img/rolex.svg', name: 'Rolex' },
        ],
        specialPrizes: [
            {
                id: 'grand_prix',
                nominations: [
                    { logo: '/img/migros.png', name: 'Migros', isWinner: true },
                    {
                        logo: '/img/nestle.png',
                        name: 'Nestlé',
                        isWinner: false,
                    },
                    { logo: '/img/rolex.svg', name: 'Rolex', isWinner: false },
                ],
            },
            {
                id: 'progression',
                nominations: [
                    { logo: '/img/nestle.png', name: 'Nestlé', isWinner: true },
                    { logo: '/img/rolex.svg', name: 'Rolex', isWinner: false },
                    {
                        logo: '/img/migros.png',
                        name: 'Migros',
                        isWinner: false,
                    },
                ],
            },
            {
                id: 'fidelite',
                nominations: [
                    { logo: '/img/rolex.svg', name: 'Rolex', isWinner: true },
                    {
                        logo: '/img/migros.png',
                        name: 'Migros',
                        isWinner: false,
                    },
                    {
                        logo: '/img/nestle.png',
                        name: 'Nestlé',
                        isWinner: false,
                    },
                ],
            },
            {
                id: 'coup_de_coeur',
                nominations: [
                    { logo: '/img/nestle.png', name: 'Nestlé', isWinner: true },
                    { logo: '/img/rolex.svg', name: 'Rolex', isWinner: false },
                    {
                        logo: '/img/migros.png',
                        name: 'Migros',
                        isWinner: false,
                    },
                ],
            },
        ],
    },
    {
        year: 2008,
        winners: [
            { rank: 1, logo: '/img/rolex.svg', name: 'Rolex' },
            { rank: 2, logo: '/img/migros.png', name: 'Migros' },
            { rank: 3, logo: '/img/nestle.png', name: 'Nestlé' },
        ],
        specialPrizes: [
            {
                id: 'grand_prix',
                nominations: [
                    { logo: '/img/rolex.svg', name: 'Rolex', isWinner: true },
                    {
                        logo: '/img/nestle.png',
                        name: 'Nestlé',
                        isWinner: false,
                    },
                    {
                        logo: '/img/migros.png',
                        name: 'Migros',
                        isWinner: false,
                    },
                ],
            },
            {
                id: 'progression',
                nominations: [
                    { logo: '/img/migros.png', name: 'Migros', isWinner: true },
                    { logo: '/img/rolex.svg', name: 'Rolex', isWinner: false },
                    {
                        logo: '/img/nestle.png',
                        name: 'Nestlé',
                        isWinner: false,
                    },
                ],
            },
            {
                id: 'fidelite',
                nominations: [
                    { logo: '/img/nestle.png', name: 'Nestlé', isWinner: true },
                    {
                        logo: '/img/migros.png',
                        name: 'Migros',
                        isWinner: false,
                    },
                    { logo: '/img/rolex.svg', name: 'Rolex', isWinner: false },
                ],
            },
            {
                id: 'coup_de_coeur',
                nominations: [
                    { logo: '/img/rolex.svg', name: 'Rolex', isWinner: true },
                    {
                        logo: '/img/migros.png',
                        name: 'Migros',
                        isWinner: false,
                    },
                    {
                        logo: '/img/nestle.png',
                        name: 'Nestlé',
                        isWinner: false,
                    },
                ],
            },
        ],
    },
];
