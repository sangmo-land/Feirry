import { chromium } from 'playwright';

const base = process.env.BASE_URL ?? 'http://127.0.0.1:8000';

const pages = [
    '/', '/entreprise', '/metiers', '/metiers/gros-oeuvre', '/realisations',
    '/realisations/residence-les-palmiers-bonapriso', '/methode', '/equipe', '/actualites',
    '/actualites/permis-de-construire-douala-guide', '/carrieres',
    '/carrieres/conducteur-de-travaux-batiment', '/faq', '/contact', '/devis', '/mentions-legales',
];

const sizes = [[360, 780], [390, 844], [768, 1024], [1024, 768], [1280, 800], [1440, 900]];

const browser = await chromium.launch({ channel: 'chrome' });
const problems = [];

for (const [w, h] of sizes) {
    const ctx = await browser.newContext({ viewport: { width: w, height: h } });
    await ctx.addInitScript(() => {
        try { sessionStorage.setItem('feirry:seen', '1'); } catch { /* private mode */ }
    });

    for (const path of pages) {
        const page = await ctx.newPage();

        try {
            await page.goto(base + path, { waitUntil: 'domcontentloaded', timeout: 30000 });
            await page.waitForTimeout(1400);

            const report = await page.evaluate((vw) => {
                const docWidth = document.documentElement.scrollWidth;
                if (docWidth <= vw + 1) return null;

                // Only elements that are NOT clipped by an ancestor can push the
                // document wide; everything else is a deliberate bleed.
                const clipped = (el) => {
                    for (let p = el.parentElement; p; p = p.parentElement) {
                        const s = getComputedStyle(p);
                        if (s.overflowX !== 'visible' || s.position === 'fixed') return true;
                    }
                    return false;
                };

                const culprits = [];
                for (const el of document.querySelectorAll('body *')) {
                    const r = el.getBoundingClientRect();
                    if (r.width === 0 || r.height === 0) continue;
                    if (getComputedStyle(el).position === 'fixed') continue;
                    if (r.right <= vw + 2) continue;
                    if (clipped(el)) continue;

                    culprits.push(
                        `<${el.tagName.toLowerCase()} class="${String(el.className).slice(0, 60)}"> right=${Math.round(r.right)} w=${Math.round(r.width)} text="${(el.textContent || '').trim().slice(0, 40)}"`,
                    );
                    if (culprits.length >= 3) break;
                }

                return `doc=${docWidth} :: ${culprits.join(' ;; ') || '(no unclipped culprit found)'}`;
            }, w);

            if (report) problems.push(`${w}px ${path}\n    ${report}`);
        } catch (e) {
            problems.push(`${w}px ${path}: ${e.message.split('\n')[0]}`);
        }

        await page.close();
    }

    await ctx.close();
}

await browser.close();
console.log(problems.length ? problems.join('\n') : 'NO HORIZONTAL OVERFLOW AT ANY BREAKPOINT');
