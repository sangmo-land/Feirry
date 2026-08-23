import { useState } from 'react';
import { AnimatePresence, motion } from 'motion/react';
import { cn, EASE } from '@/Lib/utils';
import { Icon } from '@/Components/Ui';

export function Accordion({ items = [], className, defaultOpen = null }) {
    const [open, setOpen] = useState(defaultOpen);

    return (
        <div className={cn('divide-y divide-concrete-200 border-y border-concrete-200', className)}>
            {items.map((item, index) => {
                const isOpen = open === index;

                return (
                    <motion.div
                        key={item.id ?? index}
                        initial={{ opacity: 0, y: 18 }}
                        whileInView={{ opacity: 1, y: 0 }}
                        viewport={{ once: true, amount: 0.4 }}
                        transition={{ duration: 0.7, delay: index * 0.05, ease: EASE.outExpo }}
                    >
                        <h3>
                            <button
                                type="button"
                                onClick={() => setOpen(isOpen ? null : index)}
                                aria-expanded={isOpen}
                                className="group/row flex w-full items-start justify-between gap-6 py-6 text-left sm:py-7"
                            >
                                <span className="flex gap-5">
                                    <span className="mt-1 shrink-0 font-mono text-[0.6875rem] tracking-[0.16em] text-concrete-400">
                                        {String(index + 1).padStart(2, '0')}
                                    </span>
                                    <span
                                        className={cn(
                                            'font-display text-lg font-bold leading-snug tracking-[-0.02em] transition-colors duration-400 sm:text-xl',
                                            isOpen ? 'text-brand-deep' : 'text-ink-900 group-hover/row:text-concrete-600',
                                        )}
                                    >
                                        {item.question}
                                    </span>
                                </span>

                                <span
                                    className={cn(
                                        'mt-0.5 flex size-9 shrink-0 items-center justify-center rounded-full border transition-all duration-500',
                                        isOpen
                                            ? 'rotate-45 border-brand bg-brand text-ink-950'
                                            : 'border-concrete-300 text-ink-900 group-hover/row:border-ink-900',
                                    )}
                                >
                                    <Icon name="plus" className="size-4" strokeWidth={2} />
                                </span>
                            </button>
                        </h3>

                        <AnimatePresence initial={false}>
                            {isOpen && (
                                <motion.div
                                    key="content"
                                    initial={{ height: 0, opacity: 0 }}
                                    animate={{ height: 'auto', opacity: 1 }}
                                    exit={{ height: 0, opacity: 0 }}
                                    transition={{ duration: 0.55, ease: EASE.inOutQuint }}
                                    className="overflow-hidden"
                                >
                                    <div className="pb-8 pl-[3.25rem] pr-12">
                                        <p className="max-w-3xl text-sm leading-relaxed text-concrete-600 sm:text-base">
                                            {item.answer}
                                        </p>
                                    </div>
                                </motion.div>
                            )}
                        </AnimatePresence>
                    </motion.div>
                );
            })}
        </div>
    );
}
