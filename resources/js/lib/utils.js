import { clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs) {
    return twMerge(clsx(inputs));
}

export function toUrl(href) {
    return typeof href === 'string' ? href : href?.url;
}

export function formatDay(day) {
    if (!day) {
        return '-';
    }

    return new Date(day).toLocaleDateString('fr-CH', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    });
}
