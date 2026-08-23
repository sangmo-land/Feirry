/**
 * Translation between the site's two spellings of the same page.
 *
 * Every internal href in this app is written in its French form — that is the
 * canonical one, it owns the root, and it is what the route file registers
 * unprefixed. `localizePath` renders that form in whichever language is
 * active, so a link written once works in both.
 *
 * `app/Support/LocalePath.php` is the same logic on the server, and both read
 * the one map in `config/site.segments`, shared through Inertia. Only the
 * first segment is translated: record slugs live in a single untranslated
 * column, so `/en/expertise/gros-oeuvre` is as far as English currently goes.
 */

/** Anything with a scheme, or a bare fragment, is not ours to rewrite. */
function isInternal(href) {
    return typeof href === 'string' && href.startsWith('/');
}

/** Query and fragment ride along untouched. */
function split(path) {
    const cut = path.search(/[?#]/);

    return cut === -1 ? [path, ''] : [path.slice(0, cut), path.slice(cut)];
}

/** Strip any locale prefix and put the first segment back into French. */
function canonical(path, { segments = {}, locales = {}, defaultLocale = 'fr' } = {}) {
    const parts = path.split('/').filter(Boolean);

    if (parts[0] in locales && parts[0] !== defaultLocale) parts.shift();
    if (parts.length === 0) return parts;

    const french = Object.keys(segments).find((key) => segments[key] === parts[0]);
    if (french) parts[0] = french;

    return parts;
}

/** Render a path — in either spelling — in the given locale. */
export function localizePath(href, locale, config = {}) {
    if (!isInternal(href)) return href;

    const { segments = {}, defaultLocale = 'fr' } = config;
    const [path, suffix] = split(href);
    const parts = canonical(path, config);

    if (parts.length === 0) {
        return (locale === defaultLocale ? '/' : `/${locale}`) + suffix;
    }

    if (locale !== defaultLocale) {
        parts[0] = segments[parts[0]] ?? parts[0];
        parts.unshift(locale);
    }

    return `/${parts.join('/')}${suffix}`;
}
