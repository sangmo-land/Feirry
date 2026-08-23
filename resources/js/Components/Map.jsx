import { cn } from '@/Lib/utils';
import { useSite } from '@/Hooks';
import { Icon } from '@/Components/Ui';

/**
 * The office on a map, with a link out for directions.
 *
 * Two providers, because the site wants different things in the two places it
 * shows a map:
 *
 *   `google` — what the client asked for on the company page. The `output=embed`
 *     form needs no API key, but it is still Google in an iframe, so it sets
 *     their cookies for anyone who loads the page.
 *   `osm`  — OpenStreetMap's embed. No key and no advertising cookies, which is
 *     why the contact page shipped with it.
 *
 * The pin comes from the site settings, so the client can move it from the
 * dashboard without a deploy.
 */
export function OfficeMap({
    provider = 'google',
    zoom = 16,
    className,
    ratio = 'aspect-[4/3]',
    label,
}) {
    const { site, t, locale } = useSite();

    const lat = Number(site.contact?.latitude ?? 4.047588);
    const lon = Number(site.contact?.longitude ?? 9.728659);

    // OpenStreetMap's embed frames a bounding box rather than taking a zoom
    // level, so the two are not interchangeable.
    const pad = 0.008;
    const src =
        provider === 'osm'
            ? 'https://www.openstreetmap.org/export/embed.html' +
              `?bbox=${lon - pad}%2C${lat - pad}%2C${lon + pad}%2C${lat + pad}` +
              `&layer=mapnik&marker=${lat}%2C${lon}`
            : `https://www.google.com/maps?q=${lat},${lon}&z=${zoom}&hl=${locale}&output=embed`;

    const title = [
        site.brand?.name,
        [site.contact?.district, site.contact?.city].filter(Boolean).join(', '),
    ]
        .filter(Boolean)
        .join(' — ');

    return (
        <div
            className={cn(
                'relative overflow-hidden border border-concrete-200 bg-ink-900',
                ratio,
                className,
            )}
        >
            <iframe
                title={title}
                src={src}
                loading="lazy"
                referrerPolicy="no-referrer-when-downgrade"
                className="absolute inset-0 h-full w-full border-0"
            />

            {site.contact?.maps_url && (
                <a
                    href={site.contact.maps_url}
                    target="_blank"
                    rel="noreferrer"
                    className="group/frame absolute bottom-3 right-3 inline-flex items-center gap-2 rounded-full bg-ink-950/90 px-4 py-2.5 font-mono text-[0.625rem] font-semibold uppercase tracking-[0.14em] text-concrete-100 backdrop-blur transition-colors duration-400 hover:bg-brand hover:text-ink-950"
                >
                    <Icon name="map-pin" className="size-3.5" strokeWidth={2} />
                    {label ?? t('contact.find_us')}
                    <Icon
                        name="arrow-up-right"
                        className="size-3.5 transition-transform duration-500 [transition-timing-function:var(--ease-out-expo)] group-hover/frame:translate-x-0.5 group-hover/frame:-translate-y-0.5"
                        strokeWidth={2}
                    />
                </a>
            )}
        </div>
    );
}
