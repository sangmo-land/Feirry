import { useCallback, useEffect, useState } from 'react';
import useEmblaCarousel from 'embla-carousel-react';
import { AnimatePresence, motion } from 'motion/react';
import { cn, EASE, initials } from '@/Lib/utils';
import { Icon } from '@/Components/Ui';

export function TestimonialSlider({ testimonials = [], tone = 'dark' }) {
    const [emblaRef, embla] = useEmblaCarousel({ loop: true, align: 'start', duration: 32 });
    const [selected, setSelected] = useState(0);

    const onSelect = useCallback(() => {
        if (!embla) return;
        setSelected(embla.selectedScrollSnap());
    }, [embla]);

    useEffect(() => {
        if (!embla) return undefined;
        onSelect();
        embla.on('select', onSelect);
        embla.on('reInit', onSelect);
        return () => {
            embla.off('select', onSelect);
            embla.off('reInit', onSelect);
        };
    }, [embla, onSelect]);

    if (!testimonials.length) return null;

    const light = tone === 'light';

    return (
        <div className="relative">
            <div className="overflow-hidden" ref={emblaRef}>
                <div className="flex touch-pan-y">
                    {testimonials.map((item, index) => (
                        <div key={item.id} className="min-w-0 shrink-0 grow-0 basis-full pr-6 lg:basis-1/2 lg:pr-10">
                            <figure
                                className={cn(
                                    'flex h-full flex-col justify-between border p-8 sm:p-11',
                                    light
                                        ? 'border-ink-700 bg-ink-850'
                                        : 'border-concrete-200 bg-concrete-50',
                                )}
                            >
                                <div>
                                    <div className="flex items-center gap-1">
                                        {Array.from({ length: item.rating }).map((_, star) => (
                                            <Icon
                                                key={star}
                                                name="star"
                                                className="size-4 fill-brand text-brand"
                                            />
                                        ))}
                                    </div>

                                    <Icon
                                        name="quote"
                                        className={cn(
                                            'mt-7 size-9 rotate-180',
                                            light ? 'text-ink-600' : 'text-concrete-300',
                                        )}
                                        strokeWidth={1.2}
                                    />

                                    <blockquote
                                        className={cn(
                                            'mt-5 font-display text-[clamp(1.1rem,1.9vw,1.55rem)] font-semibold leading-[1.4] tracking-[-0.02em]',
                                            light ? 'text-concrete-100' : 'text-ink-900',
                                        )}
                                    >
                                        {item.content}
                                    </blockquote>
                                </div>

                                <figcaption
                                    className={cn(
                                        'mt-9 flex items-center gap-4 border-t pt-7',
                                        light ? 'border-ink-700' : 'border-concrete-200',
                                    )}
                                >
                                    <span
                                        className={cn(
                                            'flex size-12 shrink-0 items-center justify-center rounded-full font-display text-sm font-black',
                                            light ? 'bg-brand text-ink-950' : 'bg-ink-900 text-brand',
                                        )}
                                    >
                                        {initials(item.author_name)}
                                    </span>
                                    <div className="min-w-0">
                                        <span
                                            className={cn(
                                                'block truncate font-display text-base font-bold tracking-[-0.02em]',
                                                light ? 'text-concrete-50' : 'text-ink-900',
                                            )}
                                        >
                                            {item.author_name}
                                        </span>
                                        <span
                                            className={cn(
                                                'mt-0.5 block truncate font-mono text-[0.625rem] uppercase tracking-[0.14em]',
                                                light ? 'text-concrete-400' : 'text-concrete-500',
                                            )}
                                        >
                                            {[item.author_role, item.company].filter(Boolean).join(' · ')}
                                        </span>
                                    </div>
                                </figcaption>
                            </figure>
                        </div>
                    ))}
                </div>
            </div>

            <div className="mt-10 flex items-center justify-between gap-6">
                <div className="flex gap-2">
                    {testimonials.map((item, index) => (
                        <button
                            key={item.id}
                            type="button"
                            onClick={() => embla?.scrollTo(index)}
                            aria-label={`Témoignage ${index + 1}`}
                            className={cn(
                                'h-0.5 transition-all duration-500',
                                index === selected
                                    ? 'w-12 bg-brand'
                                    : light
                                      ? 'w-6 bg-ink-600 hover:bg-ink-500'
                                      : 'w-6 bg-concrete-300 hover:bg-concrete-400',
                            )}
                        />
                    ))}
                </div>

                <div className="flex gap-3">
                    {[
                        { dir: 'prev', icon: 'arrow-left' },
                        { dir: 'next', icon: 'arrow-right' },
                    ].map(({ dir, icon }) => (
                        <button
                            key={dir}
                            type="button"
                            onClick={() => (dir === 'prev' ? embla?.scrollPrev() : embla?.scrollNext())}
                            aria-label={dir === 'prev' ? 'Précédent' : 'Suivant'}
                            className={cn(
                                'flex size-12 items-center justify-center rounded-full border transition-colors duration-400',
                                light
                                    ? 'border-ink-600 text-concrete-200 hover:border-brand hover:bg-brand hover:text-ink-950'
                                    : 'border-concrete-300 text-ink-900 hover:border-ink-900 hover:bg-ink-900 hover:text-concrete-50',
                            )}
                        >
                            <Icon name={icon} className="size-4" strokeWidth={2} />
                        </button>
                    ))}
                </div>
            </div>
        </div>
    );
}

/** A single pull-quote, used inside project case studies. */
export function PullQuote({ testimonial, className }) {
    if (!testimonial) return null;

    return (
        <motion.figure
            initial={{ opacity: 0, y: 30 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true, amount: 0.3 }}
            transition={{ duration: 0.9, ease: EASE.outExpo }}
            className={cn('border-l-2 border-brand pl-8 sm:pl-12', className)}
        >
            <blockquote className="font-display text-[clamp(1.25rem,2.6vw,2rem)] font-semibold leading-[1.35] tracking-[-0.025em] text-ink-900">
                {testimonial.content}
            </blockquote>
            <figcaption className="mt-6 font-mono text-[0.6875rem] uppercase tracking-[0.16em] text-concrete-500">
                {testimonial.author_name}
                {testimonial.author_role && <span className="text-concrete-400"> · {testimonial.author_role}</span>}
                {testimonial.company && <span className="text-concrete-400"> · {testimonial.company}</span>}
            </figcaption>
        </motion.figure>
    );
}
