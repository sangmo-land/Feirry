import { chromium } from 'playwright';

const base = process.env.BASE_URL ?? 'http://127.0.0.1:8000';
const out = process.argv[2];

const browser = await chromium.launch({ channel: 'chrome' });
const context = await browser.newContext({ viewport: { width: 1440, height: 900 } });
await context.addInitScript(() => {
    try { sessionStorage.setItem('feirry:seen', '1'); } catch { /* private mode */ }
});

const problems = [];
const page = await context.newPage();
page.on('pageerror', (e) => problems.push('pageerror: ' + e.message.slice(0, 160)));
page.on('console', (m) => {
    if (m.type() === 'error' && !/favicon|fonts\.gstatic|net::ERR_/i.test(m.text())) {
        problems.push('console: ' + m.text().slice(0, 160));
    }
});

// innerText turns the spaces between the masked heading words into NBSP,
// so every text assertion compares on normalised whitespace.
const text = async () => (await page.locator('body').innerText()).replace(/\s+/g, ' ');

/* ---------------------------------------------------------------- */
/* Client-side navigation across the whole menu                      */
/* ---------------------------------------------------------------- */
await page.goto(`${base}/`, { waitUntil: 'networkidle' });
await page.waitForTimeout(1500);

// Contact is no longer in the bar — it is reached from the closing CTA band
// and the footer instead, both covered further down.
for (const label of ['Entreprise', 'Métiers', 'Réalisations', 'Méthode', 'Équipe', 'Actualités', 'Carrières']) {
    // Matched on the accessible name, not the text. WipeText paints the link
    // twice — a resting copy and the accent copy that wipes across it — so the
    // element's textContent reads "EntrepriseEntreprise" and no ^…$ pattern
    // will ever hit it. Both layers are aria-hidden and the header puts the
    // real name on the anchor, which is the thing to match.
    await page.locator(`header nav a[aria-label="${label}"]`).first().click();
    await page.waitForTimeout(1800);

    const body = await text();
    if (body.length < 400) problems.push(`nav ${label}: page came back nearly empty`);
}

// …and back to the home page, which owns the pinned horizontal section.
await page.locator('header a[href="/"]').first().click();
await page.waitForTimeout(2000);
if (!(await text()).includes('Nous construisons')) problems.push('nav home: hero missing after return');
await page.screenshot({ path: `${out}/nav-home-return.png` });

// Contact left the bar, so check the route a visitor now takes to it.
await page.locator('section.bg-brand a[href="/contact"]').first().click();
await page.waitForTimeout(2000);
if (!/Bonanjo/i.test(await text())) problems.push('cta band: contact link did not reach the contact page');

/* ---------------------------------------------------------------- */
/* The quote wizard: three steps, then a reference number            */
/* ---------------------------------------------------------------- */
await page.goto(`${base}/devis`, { waitUntil: 'networkidle' });
await page.waitForTimeout(1500);

await page.getByText('Résidentiel individuel').click();
await page.getByRole('button', { name: /Suivant/i }).click();
await page.waitForTimeout(700);

await page.getByLabel(/Lieu du chantier/i).fill('Bonapriso, Douala');
await page.getByLabel(/Surface approximative/i).fill('420');
await page.getByText('50 à 150 M FCFA').click();
await page.getByText('Sous 6 mois').click();
await page.getByRole('button', { name: /Suivant/i }).click();
await page.waitForTimeout(700);

await page.getByLabel(/Nom et prénom/i).fill('Test Playwright');
await page.getByLabel(/Adresse e-mail/i).fill('verif@example.cm');
await page.getByLabel(/Téléphone/i).fill('+237 6 99 00 11 22');
await page.getByLabel(/Précisions utiles/i).fill('Vérification automatisée du parcours de devis.');
await page.screenshot({ path: `${out}/quote-step3.png` });

await page.getByRole('button', { name: /Envoyer ma demande/i }).click();
await page.waitForTimeout(3000);
await page.screenshot({ path: `${out}/quote-success.png` });

if (!/DEV-\d{6}-[A-Z0-9]{4}/.test(await text())) {
    problems.push('quote: no reference number shown after submit');
}

/* ---------------------------------------------------------------- */
/* Quote validation: an empty step 1 must not advance                */
/* ---------------------------------------------------------------- */
await page.goto(`${base}/devis`, { waitUntil: 'networkidle' });
await page.waitForTimeout(1200);
const next = page.getByRole('button', { name: /Suivant/i });
if (!(await next.isDisabled())) problems.push('quote: step 1 advances without a project type');

/* ---------------------------------------------------------------- */
/* Contact form                                                      */
/* ---------------------------------------------------------------- */
await page.goto(`${base}/contact`, { waitUntil: 'networkidle' });
await page.waitForTimeout(1500);

await page.getByLabel(/Nom et prénom/i).fill('Test Playwright');
await page.getByLabel(/Adresse e-mail/i).fill('verif@example.cm');
await page.getByLabel(/Votre message/i).fill('Message de vérification automatisée, suffisamment long.');
await page.getByRole('button', { name: /^Envoyer$/i }).click();
await page.waitForTimeout(2500);
await page.screenshot({ path: `${out}/contact-success.png` });

if (!/48 heures ouvrées/i.test(await text())) problems.push('contact: success message not shown');

/* ---------------------------------------------------------------- */
/* Locale switch                                                     */
/* ---------------------------------------------------------------- */
await page.goto(`${base}/`, { waitUntil: 'networkidle' });
await page.waitForTimeout(1200);
await page.getByRole('link', { name: 'EN', exact: true }).click();
await page.waitForTimeout(2500);
await page.screenshot({ path: `${out}/home-en.png` });

if (!/We build/.test(await text())) problems.push('locale: English copy not rendered');

// The English address, not the French one. Locale is a property of the URL
// now rather than of the session — SetLocale reads it off the first path
// segment — so /realisations answers in French however the switcher was last
// clicked, and asking it for English copy could only ever fail.
await page.goto(`${base}/en/projects`, { waitUntil: 'networkidle' });
await page.waitForTimeout(1500);
if (!/Delivered|In progress/i.test(await text())) {
    problems.push('locale: project statuses not translated');
}
await page.screenshot({ path: `${out}/projects-en.png` });

// Back to French, which also exercises the switcher in the other direction.
await page.getByRole('link', { name: 'FR', exact: true }).click();
await page.waitForTimeout(1500);

await browser.close();
console.log(problems.length ? problems.join('\n') : 'FLOWS ALL CLEAN');
