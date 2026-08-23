import { useId, useState } from 'react';
import { motion } from 'motion/react';
import { cn, EASE } from '@/Lib/utils';
import { useSite } from '@/Hooks';
import { Icon } from '@/Components/Ui';

/* ------------------------------------------------------------------ */
/* Field wrapper: floating label, error slot, consistent spacing.      */
/* ------------------------------------------------------------------ */
function FieldShell({ id, label, error, required, hint, children, className, tone }) {
    const dark = tone === 'dark';

    return (
        <div className={cn('relative', className)}>
            <label
                htmlFor={id}
                className={cn(
                    'mb-2 block font-mono text-[0.625rem] font-semibold uppercase tracking-[0.16em]',
                    dark ? 'text-concrete-400' : 'text-concrete-500',
                )}
            >
                {label}
                {required && <span className="ml-1 text-brand-deep">*</span>}
            </label>

            {children}

            {hint && !error && (
                <p className={cn('mt-2 text-xs', dark ? 'text-concrete-600' : 'text-concrete-400')}>{hint}</p>
            )}

            {error && (
                <motion.p
                    initial={{ opacity: 0, y: -4 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.3 }}
                    className="mt-2 flex items-center gap-1.5 text-xs text-clay"
                >
                    <Icon name="circle-alert" className="size-3.5 shrink-0" />
                    {error}
                </motion.p>
            )}
        </div>
    );
}

const inputBase =
    'w-full border-b bg-transparent px-0 py-3.5 text-sm outline-none transition-colors duration-400 placeholder:text-concrete-400';

function inputTone(dark, error) {
    if (error) return 'border-clay text-ink-900 focus:border-clay';
    return dark
        ? 'border-ink-600 text-concrete-100 placeholder:text-concrete-600 focus:border-brand'
        : 'border-concrete-300 text-ink-900 focus:border-ink-900';
}

export function TextField({
    label,
    error,
    required,
    hint,
    className,
    tone = 'light',
    type = 'text',
    ...rest
}) {
    const id = useId();
    const dark = tone === 'dark';

    return (
        <FieldShell id={id} label={label} error={error} required={required} hint={hint} className={className} tone={tone}>
            <input id={id} type={type} required={required} className={cn(inputBase, inputTone(dark, error))} {...rest} />
        </FieldShell>
    );
}

export function TextArea({ label, error, required, hint, className, tone = 'light', rows = 5, ...rest }) {
    const id = useId();
    const dark = tone === 'dark';

    return (
        <FieldShell id={id} label={label} error={error} required={required} hint={hint} className={className} tone={tone}>
            <textarea
                id={id}
                rows={rows}
                required={required}
                className={cn(inputBase, inputTone(dark, error), 'resize-y')}
                {...rest}
            />
        </FieldShell>
    );
}

export function SelectField({
    label,
    error,
    required,
    hint,
    className,
    tone = 'light',
    options = [],
    placeholder,
    ...rest
}) {
    const id = useId();
    const dark = tone === 'dark';

    return (
        <FieldShell id={id} label={label} error={error} required={required} hint={hint} className={className} tone={tone}>
            <div className="relative">
                <select
                    id={id}
                    required={required}
                    className={cn(inputBase, inputTone(dark, error), 'appearance-none pr-8')}
                    {...rest}
                >
                    {placeholder && <option value="">{placeholder}</option>}
                    {options.map((option) => (
                        <option key={option.value} value={option.value}>
                            {option.label}
                        </option>
                    ))}
                </select>
                <Icon
                    name="chevron-down"
                    className={cn(
                        'pointer-events-none absolute right-0 top-1/2 size-4 -translate-y-1/2',
                        dark ? 'text-concrete-500' : 'text-concrete-400',
                    )}
                />
            </div>
        </FieldShell>
    );
}

/* ------------------------------------------------------------------ */
/* OptionCards: a radio group rendered as selectable tiles.            */
/* ------------------------------------------------------------------ */
export function OptionCards({ label, name, options = [], value, onChange, error, required, columns = 2, tone = 'light' }) {
    return (
        <fieldset>
            <legend
                className={cn(
                    'mb-4 block font-mono text-[0.625rem] font-semibold uppercase tracking-[0.16em]',
                    tone === 'dark' ? 'text-concrete-400' : 'text-concrete-500',
                )}
            >
                {label}
                {required && <span className="ml-1 text-brand-deep">*</span>}
            </legend>

            <div
                className={cn(
                    'grid gap-2',
                    columns === 2 && 'sm:grid-cols-2',
                    columns === 3 && 'sm:grid-cols-2 lg:grid-cols-3',
                    columns === 4 && 'sm:grid-cols-2 lg:grid-cols-4',
                )}
            >
                {options.map((option) => {
                    const selected = value === option.value;

                    return (
                        <label
                            key={option.value}
                            className={cn(
                                'group/opt relative flex cursor-pointer items-center gap-3 border px-4 py-3.5 transition-all duration-400',
                                selected
                                    ? 'border-ink-900 bg-ink-900 text-concrete-50'
                                    : 'border-concrete-200 bg-concrete-50 text-ink-800 hover:border-concrete-400',
                            )}
                        >
                            <input
                                type="radio"
                                name={name}
                                value={option.value}
                                checked={selected}
                                onChange={() => onChange(option.value)}
                                className="sr-only"
                            />

                            <span
                                className={cn(
                                    'flex size-4 shrink-0 items-center justify-center rounded-full border transition-colors duration-300',
                                    selected ? 'border-brand bg-brand' : 'border-concrete-300',
                                )}
                            >
                                {selected && <span className="size-1.5 rounded-full bg-ink-950" />}
                            </span>

                            {option.icon && <Icon name={option.icon} className="size-4 shrink-0 opacity-70" />}

                            <span className="text-sm leading-tight">{option.label}</span>
                        </label>
                    );
                })}
            </div>

            {error && (
                <p className="mt-3 flex items-center gap-1.5 text-xs text-clay">
                    <Icon name="circle-alert" className="size-3.5 shrink-0" />
                    {error}
                </p>
            )}
        </fieldset>
    );
}

/* ------------------------------------------------------------------ */
/* FileField: drag-and-drop aware upload with the filename echoed back.*/
/* ------------------------------------------------------------------ */
export function FileField({ label, error, hint, required, accept, onChange, value, tone = 'light' }) {
    const id = useId();
    const [dragging, setDragging] = useState(false);

    const handleFiles = (fileList) => {
        const file = fileList?.[0];
        if (file) onChange(file);
    };

    return (
        <FieldShell id={id} label={label} error={error} hint={hint} required={required} tone={tone}>
            <label
                htmlFor={id}
                onDragOver={(event) => {
                    event.preventDefault();
                    setDragging(true);
                }}
                onDragLeave={() => setDragging(false)}
                onDrop={(event) => {
                    event.preventDefault();
                    setDragging(false);
                    handleFiles(event.dataTransfer.files);
                }}
                className={cn(
                    // `min-w-0` keeps the nowrap filename from setting a floor on
                    // this box's width, which would push the whole grid column wide.
                    'flex min-w-0 cursor-pointer items-center gap-4 border border-dashed px-5 py-6 transition-colors duration-400',
                    dragging
                        ? 'border-brand bg-brand/8'
                        : error
                          ? 'border-clay'
                          : 'border-concrete-300 hover:border-ink-900',
                )}
            >
                <span className="flex size-11 shrink-0 items-center justify-center rounded-full bg-concrete-100 text-ink-900">
                    <Icon name={value ? 'paperclip' : 'upload'} className="size-4.5" />
                </span>

                <span className="min-w-0 flex-1">
                    <span className="block truncate text-sm text-ink-900">
                        {value?.name ?? 'Glissez un fichier ou cliquez pour parcourir'}
                    </span>
                    {value && (
                        <span className="mt-0.5 block font-mono text-[0.625rem] uppercase tracking-[0.12em] text-concrete-500">
                            {(value.size / 1024 / 1024).toFixed(2)} Mo
                        </span>
                    )}
                </span>

                {value && (
                    <button
                        type="button"
                        onClick={(event) => {
                            event.preventDefault();
                            onChange(null);
                        }}
                        className="shrink-0 text-concrete-400 transition-colors hover:text-clay"
                        aria-label="Retirer le fichier"
                    >
                        <Icon name="x" className="size-4" />
                    </button>
                )}

                <input
                    id={id}
                    type="file"
                    accept={accept}
                    required={required && !value}
                    onChange={(event) => handleFiles(event.target.files)}
                    className="sr-only"
                />
            </label>
        </FieldShell>
    );
}

/* ------------------------------------------------------------------ */
/* Honeypot: invisible to people, irresistible to bots.                */
/* ------------------------------------------------------------------ */
export function Honeypot({ value, onChange }) {
    return (
        <div className="hidden" aria-hidden="true">
            <label htmlFor="website-hp">Ne pas remplir</label>
            <input
                id="website-hp"
                name="website"
                type="text"
                tabIndex={-1}
                autoComplete="off"
                value={value}
                onChange={(event) => onChange(event.target.value)}
            />
        </div>
    );
}

/* ------------------------------------------------------------------ */
/* SubmitButton                                                        */
/* ------------------------------------------------------------------ */
export function SubmitButton({ children, processing, className, tone = 'brand' }) {
    const { t } = useSite();

    const tones = {
        brand: 'bg-brand text-ink-950 hover:bg-brand-bright',
        solid: 'bg-ink-900 text-concrete-50 hover:bg-ink-800',
    };

    return (
        <button
            type="submit"
            disabled={processing}
            className={cn(
                'group/submit relative inline-flex items-center justify-center gap-3 overflow-hidden rounded-full px-9 py-4',
                'font-semibold uppercase tracking-[0.14em] transition-colors duration-500',
                'text-[0.8125rem] disabled:cursor-not-allowed disabled:opacity-60',
                tones[tone],
                className,
            )}
        >
            <span className="relative z-10">{processing ? t('common.sending') : children}</span>
            <Icon
                name={processing ? 'loader-circle' : 'arrow-right'}
                className={cn(
                    'relative z-10 size-4 transition-transform duration-500 [transition-timing-function:var(--ease-out-expo)]',
                    processing ? 'animate-spin' : 'group-hover/submit:translate-x-1.5',
                )}
                strokeWidth={2}
            />
            <span className="pointer-events-none absolute inset-0 z-0 -translate-x-full bg-white/25 transition-transform duration-700 [transition-timing-function:var(--ease-out-expo)] group-hover/submit:translate-x-full" />
        </button>
    );
}

/* ------------------------------------------------------------------ */
/* FormSuccess: replaces the form once a submission lands.             */
/* ------------------------------------------------------------------ */
export function FormSuccess({ title, message, reference, onReset, resetLabel }) {
    return (
        <motion.div
            initial={{ opacity: 0, y: 24 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.7, ease: EASE.outExpo }}
            className="border border-jade/25 bg-jade/5 p-10 text-center sm:p-14"
        >
            <motion.span
                initial={{ scale: 0.5, opacity: 0 }}
                animate={{ scale: 1, opacity: 1 }}
                transition={{ duration: 0.6, delay: 0.15, ease: EASE.spring }}
                className="mx-auto flex size-16 items-center justify-center rounded-full bg-jade text-white"
            >
                <Icon name="check" className="size-8" strokeWidth={2.5} />
            </motion.span>

            <h3 className="mt-7 text-2xl text-ink-900">{title}</h3>
            <p className="mx-auto mt-4 max-w-lg text-sm leading-relaxed text-concrete-600">{message}</p>

            {reference && (
                <p className="mt-6 inline-block border border-jade/30 bg-concrete-50 px-5 py-2.5 font-mono text-xs uppercase tracking-[0.16em] text-jade">
                    {reference}
                </p>
            )}

            {onReset && (
                <button
                    type="button"
                    onClick={onReset}
                    className="mt-8 block w-full font-mono text-[0.6875rem] uppercase tracking-[0.16em] text-concrete-500 underline-offset-4 transition-colors hover:text-ink-900 hover:underline"
                >
                    {resetLabel}
                </button>
            )}
        </motion.div>
    );
}
