import '../css/app.css';

import { createInertiaApp } from '@inertiajs/react';
import { createRoot, hydrateRoot } from 'react-dom/client';
import { StrictMode } from 'react';
import SiteLayout from '@/Layouts/SiteLayout';

const appName = import.meta.env.VITE_APP_NAME || 'FEIRRY';

createInertiaApp({
    /*
     * A pass-through, not a formatter. The layout hands over the title the
     * server already assembled, so the wording lives in Seo::set() alone —
     * the crawler reads that string out of the Blade <head> and the tab
     * shows the same one, which is not true of two copies of the format.
     *
     * The fallback matters more than it looks: Inertia calls this with an
     * empty string to work out the document's default title, and the result
     * replaces whatever Blade wrote. Returning nothing here left every tab
     * on the bare brand name.
     */
    title: (title) => title || appName,

    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.jsx', { eager: true });
        const page = pages[`./Pages/${name}.jsx`];

        if (!page) {
            throw new Error(`Inertia page not found: ./Pages/${name}.jsx`);
        }

        // Every page gets the site chrome unless it opts out via `Page.layout = null`.
        page.default.layout =
            page.default.layout === undefined
                ? (pageEl) => <SiteLayout>{pageEl}</SiteLayout>
                : page.default.layout;

        return page;
    },

    setup({ el, App, props }) {
        const tree = (
            <StrictMode>
                <App {...props} />
            </StrictMode>
        );

        if (import.meta.env.SSR) {
            hydrateRoot(el, tree);
            return;
        }

        createRoot(el).render(tree);
    },

    progress: {
        color: '#fe5000',
        height: '3px',
        showSpinner: false,
    },
});
