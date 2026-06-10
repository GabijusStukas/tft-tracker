export function normalizeTraitName(name: string): string {
    return name
        .replace(/^TFT\d+_/, '')
        .replace(/(UniqueTrait|Trait)$/, '')
        .replace(/_/g, ' ');
}

export function formatMatchDate(date: string): string {
    const parsed = new Date(date);

    if (Number.isNaN(parsed.getTime())) {
        return date;
    }

    const year = parsed.getUTCFullYear();
    const month = String(parsed.getUTCMonth() + 1).padStart(2, '0');
    const day = String(parsed.getUTCDate()).padStart(2, '0');
    const hours = String(parsed.getUTCHours()).padStart(2, '0');
    const minutes = String(parsed.getUTCMinutes()).padStart(2, '0');
    const seconds = String(parsed.getUTCSeconds()).padStart(2, '0');

    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}

