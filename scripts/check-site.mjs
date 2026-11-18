#!/usr/bin/env node
/*
 * CDC correctif OPS-01 — contrôle non destructif du site public.
 * Lecture seule : aucune donnée envoyée, aucun formulaire soumis.
 *
 *   node scripts/check-site.mjs                       # https://feirry.org
 *   node scripts/check-site.mjs https://preprod.exemple
 *
 * Pour chaque adresse du plan du site : statut HTTP, redirection, titre, H1 du
 * HTML servi, canonique, hreflang réciproques, robots. Puis : variante www
 * (T01), en-têtes de sécurité, robots.txt. Sortie : tableau + code retour 1
 * si une anomalie bloquante est trouvée.
 */
const base = (process.argv[2] || 'https://feirry.org').replace(/\/$/, '');
const host = new URL(base).host;
const pick = (html, re) => (html.match(re) || [])[1]?.trim() ?? '';
const problems = [];
const flag = (url, msg) => problems.push(`${url} — ${msg}`);

async function get(url, redirect = 'follow') {
    const r = await fetch(url, { redirect, headers: { 'user-agent': 'feirry-check/1.0' } });
    return { r, html: redirect === 'follow' ? await r.text() : '' };
}

const { html: sm } = await get(`${base}/sitemap.xml`);
const urls = [...sm.matchAll(/<loc>([^<]+)<\/loc>/g)].map((m) => m[1]);
const lastmods = new Set([...sm.matchAll(/<lastmod>([^<]+)<\/lastmod>/g)].map((m) => m[1]));
console.log(`Plan du site : ${urls.length} adresses, ${lastmods.size} date(s) de modification distincte(s)\n`);

const titles = new Map();
const rows = [];
// One at a time, on purpose: the check must not load the server.
for (const url of urls) {
    const { r, html } = await get(url);
    const title = pick(html, /<title[^>]*>([^<]*)<\/title>/i);
    const canonical = pick(html, /<link[^>]+rel="canonical"[^>]+href="([^"]+)"/i);
    const robots = pick(html, /<meta[^>]+name="robots"[^>]+content="([^"]+)"/i);
    const description = pick(html, /<meta[^>]+name="description"[^>]+content="([^"]*)"/i);
    const alternates = [...html.matchAll(/<link[^>]+rel="alternate"[^>]+hreflang="([^"]+)"[^>]+href="([^"]+)"/gi)].map((m) => `${m[1]}=${m[2]}`);
    if (r.status !== 200) flag(url, `statut ${r.status}`);
    if (r.redirected) flag(url, `redirigée vers ${r.url}`);
    if (canonical.replace(/\/$/, '') !== url.replace(/\/$/, '')) flag(url, `canonique « ${canonical} »`);
    if (/noindex/i.test(robots)) flag(url, 'noindex dans le plan du site');
    if (!title) flag(url, 'titre vide');
    if (!description) flag(url, 'description vide');
    if (new URL(canonical || url).host !== host) flag(url, 'canonique hors domaine de référence');
    if (alternates.length < 3) flag(url, `hreflang incomplets (${alternates.length})`);
    titles.set(title, [...(titles.get(title) || []), url]);
    rows.push({ url: url.replace(base, '') || '/', statut: r.status, titre: title.slice(0, 48), description: description.length, hreflang: alternates.length });
}
console.table(rows);
for (const [title, list] of titles) if (list.length > 1) flag(list.join(' , '), `titre dupliqué « ${title} »`);

// T01 — www must reach the bare domain in one hop.
if (!host.startsWith('www.')) {
    for (const path of ['/', '/offres', '/en/projects']) {
        const www = `https://www.${host}${path}`;
        try {
            const { r } = await get(www, 'manual');
            const to = r.headers.get('location') || '';
            if (![301, 308].includes(r.status) || to.replace(/\/$/, '') !== `${base}${path}`.replace(/\/$/, '')) flag(www, `attendu 301 vers ${base}${path}, obtenu ${r.status} ${to}`);
        } catch (e) {
            flag(www, `injoignable (${e.message})`);
        }
    }
}

const { r: home } = await get(`${base}/`);
for (const h of ['content-security-policy', 'x-content-type-options', 'x-frame-options', 'referrer-policy', 'strict-transport-security']) {
    if (!home.headers.get(h)) flag(base, `en-tête ${h} absent`);
}
if (home.headers.get('x-powered-by')) flag(base, `en-tête x-powered-by exposé (${home.headers.get('x-powered-by')})`);

const { html: robots } = await get(`${base}/robots.txt`);
if (!/Sitemap:\s*https?:\/\//i.test(robots)) flag(`${base}/robots.txt`, 'ligne Sitemap absente');
if (/Disallow:\s*\/\s*$/m.test(robots)) flag(`${base}/robots.txt`, 'tout le site est interdit aux robots');

console.log(problems.length ? `\n${problems.length} anomalie(s) :\n- ${problems.join('\n- ')}` : '\nAucune anomalie.');
process.exit(problems.length ? 1 : 0);
