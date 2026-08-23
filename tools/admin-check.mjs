import { chromium } from 'playwright';

const base = process.env.BASE_URL ?? 'http://127.0.0.1:8000';
const out = process.argv[2];

const browser = await chromium.launch({ channel: 'chrome' });
const context = await browser.newContext({ viewport: { width: 1600, height: 1000 } });
const page = await context.newPage();

const problems = [];
const watch = (p, label) => {
    p.on('console', (m) => {
        if (m.type() === 'error' && !/favicon|fonts\.gstatic|net::ERR_/i.test(m.text())) {
            problems.push(`${label} console: ${m.text().slice(0, 160)}`);
        }
    });
    p.on('pageerror', (e) => problems.push(`${label} pageerror: ${e.message.slice(0, 200)}`));
    p.on('response', (r) => {
        if (r.status() >= 500) problems.push(`${label} HTTP ${r.status()} ${r.url().slice(0, 120)}`);
    });
};

watch(page, 'login');

await page.goto(`${base}/admin/login`, { waitUntil: 'networkidle' });
await page.fill('input[type="email"]', 'admin@feirry.org');
await page.fill('input[type="password"]', 'feirry2026');
await page.click('button[type="submit"]');
await page.waitForURL(/\/admin/, { timeout: 30000 });
await page.waitForLoadState('networkidle');
await page.waitForTimeout(4000);
await page.screenshot({ path: `${out}/admin-dashboard.png` });

if (!(await page.locator('body').innerText()).includes('devis')) {
    problems.push('dashboard: quote widget text not found');
}

// Every resource list, plus one edit form per resource, must render.
const slugs = [
    'services', 'projects', 'project-categories', 'process-steps', 'faqs',
    'team-members', 'testimonials', 'milestones', 'certifications', 'partners',
    'posts', 'post-categories', 'newsletter-subscribers',
    'job-openings', 'job-applications',
    'quote-requests', 'contact-messages',
    'site-settings-page',
];

for (const slug of slugs) {
    const p = await context.newPage();
    watch(p, slug);

    try {
        await p.goto(`${base}/admin/${slug}`, { waitUntil: 'networkidle', timeout: 40000 });
        await p.waitForTimeout(2500);

        const text = await p.locator('body').innerText();
        if (/Server Error|Whoops|Internal Server Error/i.test(text)) {
            problems.push(`${slug}: server error page`);
        }
        await p.screenshot({ path: `${out}/admin-${slug}.png` });

        // Open the first record's edit screen, where the real form lives.
        const edit = p.locator('a[href*="/edit"]').first();
        if (await edit.count()) {
            await edit.click();
            await p.waitForLoadState('networkidle');
            await p.waitForTimeout(2500);
            const editText = await p.locator('body').innerText();
            if (/Server Error|Whoops|Internal Server Error/i.test(editText)) {
                problems.push(`${slug}: edit form errored`);
            }
            await p.screenshot({ path: `${out}/admin-${slug}-edit.png` });
        }
    } catch (e) {
        problems.push(`${slug}: ${e.message.split('\n')[0]}`);
    }

    await p.close();
}

await browser.close();
console.log(problems.length ? problems.join('\n') : 'ADMIN ALL CLEAN');
