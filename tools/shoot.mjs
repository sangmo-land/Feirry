import { chromium } from 'playwright';

const base = process.env.BASE_URL ?? 'http://127.0.0.1:8000';
const out = process.argv[2];
const only = process.argv[3];
const width = Number(process.env.W ?? 1440);
const height = Number(process.env.H ?? 900);
const full = process.env.FULL === '1';
const scrollTo = process.env.SCROLL ? Number(process.env.SCROLL) : null;

const pages = [
    ['home', '/'],
    ['about', '/entreprise'],
    ['services', '/metiers'],
    ['service-show', '/metiers/gros-oeuvre'],
    ['projects', '/realisations'],
    ['project-show', '/realisations/residence-les-palmiers-bonapriso'],
    ['process', '/methode'],
    ['team', '/equipe'],
    ['blog', '/actualites'],
    ['blog-show', '/actualites/permis-de-construire-douala-guide'],
    ['careers', '/carrieres'],
    ['career-show', '/carrieres/conducteur-de-travaux-batiment'],
    ['faq', '/faq'],
    ['contact', '/contact'],
    ['quote', '/devis'],
    ['legal', '/mentions-legales'],
].filter(([name]) => !only || name === only);

const browser = await chromium.launch({ channel: 'chrome' });
const context = await browser.newContext({ viewport: { width, height }, deviceScaleFactor: 1 });
// Skip the once-per-session preloader so screenshots capture the page itself.
await context.addInitScript(() => {
    try { window.sessionStorage.setItem('feirry:seen', '1'); } catch { /* private mode */ }
});

const problems = [];

for (const [name, path] of pages) {
    const page = await context.newPage();
    const errors = [];
    page.on('console', (m) => { if (m.type() === 'error') errors.push(m.text()); });
    page.on('pageerror', (e) => errors.push('PAGEERROR: ' + e.message));

    try {
        await page.goto(base + path, { waitUntil: 'networkidle', timeout: 45000 });
        await page.waitForTimeout(1200);
        // Screenshots are worthless while hero plates are still in flight.
        await page.evaluate(() => Promise.all(
            [...document.images].filter((i) => !i.complete).map((i) => new Promise((res) => {
                i.addEventListener('load', res, { once: true });
                i.addEventListener('error', res, { once: true });
                setTimeout(res, 12000);
            })),
        ));
        await page.waitForTimeout(1200);

        if (scrollTo !== null) {
            // Teleporting past a section skips its scroll-triggered reveals,
            // so walk down the page the way a visitor would.
            for (let y = 200; y <= scrollTo; y += 200) {
                await page.evaluate((v) => window.scrollTo(0, v), y);
                await page.waitForTimeout(220);
            }
            await page.evaluate((y) => window.scrollTo(0, y), scrollTo);
            await page.waitForTimeout(1200);
            await page.evaluate(() => Promise.all(
                [...document.images].filter((i) => !i.complete).map((i) => new Promise((res) => {
                    i.addEventListener('load', res, { once: true });
                    i.addEventListener('error', res, { once: true });
                    setTimeout(res, 10000);
                })),
            ));
            await page.waitForTimeout(1500);
        }

        await page.screenshot({ path: `${out}/${name}.png`, fullPage: full });

        const text = (await page.locator('body').innerText()).trim();
        if (text.length < 200) problems.push(`${name}: page looks empty (${text.length} chars)`);
    } catch (e) {
        problems.push(`${name}: ${e.message.split('\n')[0]}`);
    }

    const real = errors.filter((e) => !/favicon|fonts\.gstatic|net::ERR_|Failed to load resource.*40[34]/i.test(e));
    if (real.length) problems.push(`${name}: ${real.slice(0, 4).join(' | ')}`);
    await page.close();
}

await browser.close();
console.log(problems.length ? problems.join('\n') : 'ALL CLEAN');
