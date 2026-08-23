import { clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs) {
    return twMerge(clsx(inputs));
}

/** French-style thousand separators, which is what XAF amounts use locally. */
export function formatNumber(value, locale = 'fr') {
    if (value === null || value === undefined || value === '') return '';
    return new Intl.NumberFormat(locale === 'fr' ? 'fr-FR' : 'en-GB').format(value);
}

export function formatDate(value, locale = 'fr', options = {}) {
    if (!value) return '';
    return new Intl.DateTimeFormat(locale === 'fr' ? 'fr-FR' : 'en-GB', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        ...options,
    }).format(new Date(value));
}

export function initials(name = '') {
    return name
        .split(/\s+/)
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0])
        .join('')
        .toUpperCase();
}

/**
 * Fill `:token` placeholders in a string.
 *
 * Shared with the hero copy, which now comes out of the database: the seeded
 * home lead carries `:year`, and it has to resolve whichever side it is read
 * from or the page prints the token.
 */
export function interpolate(value, replacements = {}) {
    if (typeof value !== 'string') return value;

    return Object.entries(replacements).reduce(
        (text, [token, replacement]) => text.replaceAll(`:${token}`, replacement),
        value,
    );
}

/** Resolve a dotted key against the shared translations payload. */
export function translate(dictionary, key, replacements = {}) {
    const value = key.split('.').reduce((carry, part) => carry?.[part], dictionary);

    if (typeof value !== 'string') return value ?? key;

    return interpolate(value, replacements);
}

export const EASE = {
    outExpo: [0.16, 1, 0.3, 1],
    inOutQuint: [0.83, 0, 0.17, 1],
    spring: [0.34, 1.56, 0.64, 1],
};
