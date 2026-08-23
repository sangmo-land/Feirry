import { forwardRef } from 'react';
import { Link } from '@/Components/Link';
import { motion } from 'motion/react';
import { FALLBACK_ICON, ICONS } from '@/Lib/icons';
import { cn, EASE, initials } from '@/Lib/utils';
import { useMagnetic } from '@/Hooks';
import { Bar, Reveal, Rule, SplitText } from '@/Components/Motion';

/* ------------------------------------------------------------------ */
/* Icon: resolves a kebab-case name to a Lucide component.             */
/* ------------------------------------------------------------------ */
export function Icon({ name, className, strokeWidth = 1.6, ...rest }) {
    const Component = ICONS[String(name ?? '').toLowerCase()] ?? FALLBACK_ICON;

    return <Component className={className} strokeWidth={strokeWidth} aria-hidden="true" {...rest} />;
}

/* ------------------------------------------------------------------ */
/* Button: three tones, magnetic pull, and a sweep on hover.           */
/* ------------------------------------------------------------------ */
const BUTTON_TONES = {
    solid: 'bg-ink-900 text-concrete-50 hover:bg-ink-800',
    brand: 'bg-brand text-ink-950 hover:bg-brand-bright',
    outline: 'border border-ink-900/25 text-ink-900 hover:border-ink-900 hover:bg-ink-900 hover:text-concrete-50',
    ghost: 'border border-concrete-50/25 text-concrete-50 hover:bg-concrete-50 hover:text-ink-950',
};

const BUTTON_SIZES = {
    sm: 'px-5 py-2.5 text-xs',
    md: 'px-7 py-3.5 text-[0.8125rem]',
    lg: 'px-9 py-4.5 text-sm',
};

export const Button = forwardRef(function Button(
    {
        children,
        href,
        as,
        tone = 'solid',
        size = 'md',
        icon = 'arrow-up-right',
        showIcon = true,
        className,
        magnetic = true,
        ...rest
    },
    forwardedRef,
) {
    const magneticRef = useMagnetic(magnetic ? 0.22 : 0);

    const classes = cn(
        'group/btn relative inline-flex max-w-full items-center justify-center gap-2.5 overflow-hidden rounded-full',
        'whitespace-nowrap font-semibold uppercase tracking-[0.14em] transition-colors duration-500',
        'will-change-transform [transition:transform_0.5s_var(--ease-out-expo),background-color_0.4s,color_0.4s,border-color_0.4s]',
        BUTTON_TONES[tone],
        BUTTON_SIZES[size],
        className,
    );

    const inner = (
        <>
            <span className="relative z-10">{children}</span>
            {showIcon && (
                <span className="relative z-10 overflow-hidden">
                    <Icon
                        name={icon}
                        className="size-4 transition-transform duration-500 [transition-timing-function:var(--ease-out-expo)] group-hover/btn:translate-x-4 group-hover/btn:-translate-y-4"
                        strokeWidth={2}
                    />
                    <Icon
                        name={icon}
                        className="absolute inset-0 size-4 -translate-x-4 translate-y-4 transition-transform duration-500 [transition-timing-function:var(--ease-out-expo)] group-hover/btn:translate-x-0 group-hover/btn:translate-y-0"
                        strokeWidth={2}
                    />
                </span>
            )}
            <span className="pointer-events-none absolute inset-0 z-0 -translate-x-full bg-white/15 transition-transform duration-700 [transition-timing-function:var(--ease-out-expo)] group-hover/btn:translate-x-full" />
        </>
    );

    const setRefs = (node) => {
        magneticRef.current = node;
        if (typeof forwardedRef === 'function') forwardedRef(node);
        else if (forwardedRef) forwardedRef.current = node;
    };

    if (href) {
        const External = as === 'a' || /^(https?:|mailto:|tel:)/.test(href);

        if (External) {
            return (
                <a ref={setRefs} href={href} className={classes} {...rest}>
                    {inner}
                </a>
            );
        }

        return (
            <Link ref={setRefs} href={href} className={classes} {...rest}>
                {inner}
            </Link>
        );
    }

    return (
        <button ref={setRefs} type="button" className={classes} {...rest}>
            {inner}
        </button>
    );
});

/* ------------------------------------------------------------------ */
/* Eyebrow: the small mono label above every section heading.          */
/* ------------------------------------------------------------------ */
/* `dark` sits on a light ground, `light` on a dark one, and `brand` on the
   orange band — where neither of the other two survives, the muted grey being
   unreadable and the orange marker invisible. */
const EYEBROW_TONES = {
    dark: { text: 'text-concrete-500', marker: 'bg-brand' },
    light: { text: 'text-brand', marker: 'bg-brand' },
    brand: { text: 'text-ink-900', marker: 'bg-ink-900' },
};

export function Eyebrow({ children, className, tone = 'dark' }) {
    const { text, marker } = EYEBROW_TONES[tone] ?? EYEBROW_TONES.dark;

    return (
        <span className={cn('eyebrow inline-flex items-center gap-3', text, className)}>
            <span className={cn('inline-block h-2 w-2 shrink-0 rotate-45', marker)} />
            {children}
        </span>
    );
}

/* ------------------------------------------------------------------ */
/* SectionHeading: eyebrow + split heading + optional lead and action. */
/* ------------------------------------------------------------------ */
export function SectionHeading({
    eyebrow,
    title,
    lead,
    action,
    align = 'left',
    tone = 'dark',
    className,
    titleClassName,
}) {
    return (
        <div
            className={cn(
                'flex flex-col gap-6',
                align === 'center' && 'items-center text-center',
                align === 'between' && 'lg:flex-row lg:items-end lg:justify-between lg:gap-16',
                className,
            )}
        >
            <div className={cn('max-w-3xl', align === 'center' && 'mx-auto')}>
                {eyebrow && (
                    <Reveal y={16} duration={0.7}>
                        <Eyebrow tone={tone}>{eyebrow}</Eyebrow>
                    </Reveal>
                )}

                <h2
                    className={cn(
                        'mt-5 text-[clamp(2rem,5.2vw,4.25rem)]',
                        tone === 'dark' ? 'text-ink-900' : 'text-concrete-50',
                        titleClassName,
                    )}
                >
                    <SplitText text={title} />
                </h2>

                {lead && (
                    <Reveal delay={0.15} y={20}>
                        <p
                            className={cn(
                                'mt-6 max-w-2xl text-base leading-relaxed sm:text-lg',
                                tone === 'dark' ? 'text-concrete-600' : 'text-concrete-300',
                                align === 'center' && 'mx-auto',
                            )}
                        >
                            {lead}
                        </p>
                    </Reveal>
                )}
            </div>

            {action && (
                <Reveal delay={0.2} y={16} className="shrink-0">
                    {action}
                </Reveal>
            )}
        </div>
    );
}

/* ------------------------------------------------------------------ */
/* Section: consistent vertical rhythm and optional dark treatment.    */
/* ------------------------------------------------------------------ */
export function Section({ children, className, tone = 'light', id, size = 'md', ...rest }) {
    const padding = {
        sm: 'py-12 sm:py-14',
        md: 'py-16 sm:py-20',
        lg: 'py-20 sm:py-28',
    }[size];

    return (
        <section
            id={id}
            className={cn(
                'relative',
                padding,
                tone === 'dark' && 'bg-ink-900 text-concrete-100',
                tone === 'muted' && 'bg-concrete-100',
                className,
            )}
            {...rest}
        >
            {children}
        </section>
    );
}

/* ------------------------------------------------------------------ */
/* Badge / Pill                                                        */
/* ------------------------------------------------------------------ */
export function Badge({ children, tone = 'neutral', className }) {
    const tones = {
        neutral: 'bg-concrete-100 text-concrete-700 border-concrete-200',
        brand: 'bg-brand/15 text-brand-deep border-brand/35',
        jade: 'bg-jade/10 text-jade border-jade/25',
        dark: 'bg-ink-900 text-concrete-100 border-ink-700',
        outline: 'border-current/25 text-current',
    };

    return (
        <span
            className={cn(
                'inline-flex items-center gap-2 rounded-full border px-3 py-1',
                'font-mono text-[0.625rem] font-semibold uppercase tracking-[0.16em]',
                tones[tone],
                className,
            )}
        >
            {children}
        </span>
    );
}

/* ------------------------------------------------------------------ */
/* Monogram: the avatar we use until the client uploads real photos.   */
/* ------------------------------------------------------------------ */
export function Monogram({ name, className, photo }) {
    if (photo) {
        return (
            <img
                src={photo}
                alt={name}
                loading="lazy"
                className={cn('h-full w-full object-cover', className)}
            />
        );
    }

    return (
        <div
            className={cn(
                'relative flex h-full w-full items-center justify-center overflow-hidden bg-ink-850',
                className,
            )}
        >
            <div className="blueprint-grid absolute inset-0 opacity-70" />
            <span className="relative font-display text-[clamp(2.5rem,6vw,4rem)] font-black tracking-tight text-brand">
                {initials(name)}
            </span>
            <span className="absolute bottom-0 left-0 h-1 w-full hazard-stripes opacity-80" />
        </div>
    );
}

/* ------------------------------------------------------------------ */
/* Marquee: infinite horizontal ticker, duplicated for seamless loop.  */
/* ------------------------------------------------------------------ */
export function Marquee({ children, speed = 42, reverse = false, className, itemClassName }) {
    const items = Array.isArray(children) ? children : [children];

    return (
        <div className={cn('relative flex overflow-hidden mask-fade-x', className)}>
            <div
                className={cn(
                    'flex w-max shrink-0 items-center',
                    reverse ? 'animate-marquee-reverse' : 'animate-marquee',
                )}
                style={{ '--marquee-duration': `${speed}s` }}
            >
                {[0, 1].map((copy) => (
                    <div key={copy} className="flex items-center" aria-hidden={copy === 1}>
                        {items.map((item, index) => (
                            <div key={`${copy}-${index}`} className={cn('shrink-0', itemClassName)}>
                                {item}
                            </div>
                        ))}
                    </div>
                ))}
            </div>
        </div>
    );
}

/* ------------------------------------------------------------------ */
/* HazardBar: the orange/ink diagonal stripe used as a section divider. */
/* ------------------------------------------------------------------ */
export function HazardBar({ className }) {
    return (
        <Bar
            className={cn('h-2 w-full', className)}
            fillClassName="hazard-stripes"
            duration={1.2}
            amount={0.1}
        />
    );
}

export { Bar, Rule };
