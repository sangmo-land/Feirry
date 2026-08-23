import { forwardRef } from 'react';
import { Link as InertiaLink } from '@inertiajs/react';
import { useLocalePath } from '@/Hooks';

/**
 * Inertia's Link with the site's two URL spellings folded in.
 *
 * Every internal href in this app is written in French. Routing it through
 * here means a link is written once and lands on the right address in both
 * languages, rather than each of the sixty-odd hrefs having to remember which
 * language the visitor is reading. External hrefs pass straight through.
 *
 * `localize={false}` is for the one case that already knows its destination:
 * the language switcher, whose whole purpose is to point *out* of the active
 * locale. Left on, this would translate its href back into the language the
 * visitor is leaving and the link would go nowhere.
 */
export const Link = forwardRef(function Link({ href, localize = true, ...rest }, ref) {
    const localizePath = useLocalePath();

    return <InertiaLink ref={ref} href={localize ? localizePath(href) : href} {...rest} />;
});
