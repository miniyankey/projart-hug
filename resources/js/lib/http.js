// Le cookie XSRF-TOKEN posé par Laravel sert à passer la protection CSRF des
// routes web : on le renvoie dans l'en-tête X-XSRF-TOKEN.
export function readXsrfToken() {
    const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/);

    return match ? decodeURIComponent(match[1]) : null;
}

// POST JSON « fire & forget » (non bloquant) vers une route web protégée CSRF.
// Les erreurs sont avalées : l'appelant n'attend pas de réponse exploitable.
export function postJson(url, payload = {}) {
    const xsrfToken = readXsrfToken();

    return fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            ...(xsrfToken ? { 'X-XSRF-TOKEN': xsrfToken } : {}),
        },
        credentials: 'same-origin',
        keepalive: true,
        body: JSON.stringify(payload),
    }).catch(() => {});
}
