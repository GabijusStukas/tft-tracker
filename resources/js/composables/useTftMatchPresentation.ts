export interface TftItemIcon {
    icon?: string | null;
}

const rarityColors: Record<number, string> = {
    6: '#EB9C00',
    4: '#E537A2',
    2: '#0093FF',
    1: '#00AE0A',
    0: '#9AA4AF',
};

export function useTftMatchPresentation() {
    function normalizeTraitName(name: string): string {
        return name
            .replace(/^TFT\d+_/, '')
            .replace(/(UniqueTrait|Trait)$/, '')
            .replace(/_/g, ' ');
    }

    function getUnitName(characterId?: string): string {
        if (!characterId) {
            return 'Unit';
        }

        return characterId.replace(/^TFT\d+_/, '');
    }

    function getUnitColor(rarity: number): string {
        return rarityColors[rarity] ?? '#9AA4AF';
    }

    function getUnitStyle(rarity: number): Record<string, string> {
        return {
            borderColor: getUnitColor(rarity),
        };
    }

    function getVisibleUnitItems(items: TftItemIcon[]): TftItemIcon[] {
        return items.filter((item) => Boolean(item.icon)).slice(0, 3);
    }

    function getPlacementClass(placement: number): string {
        if (placement === 1) {
            return 'border border-amber-300/70 bg-amber-50 text-amber-800 dark:border-amber-500/40 dark:bg-amber-500/10 dark:text-amber-200';
        }

        if (placement <= 4) {
            return 'border border-emerald-300/70 bg-emerald-50 text-emerald-800 dark:border-emerald-500/40 dark:bg-emerald-500/10 dark:text-emerald-200';
        }

        return 'border border-rose-300/70 bg-rose-50 text-rose-800 dark:border-rose-500/40 dark:bg-rose-500/10 dark:text-rose-200';
    }

    function getPlacementLabel(placement: number): string {
        if (placement === 1) {
            return 'Victory';
        }

        if (placement <= 4) {
            return 'Top 4';
        }

        return 'Bottom 4';
    }

    function getLastRoundLabel(lastRound: number | null): string {
        if (lastRound === null || lastRound < 1) {
            return '-';
        }

        if (lastRound <= 4) {
            return `1-${lastRound}`;
        }

        const roundsAfterStageOne = lastRound - 4;
        const stage = Math.floor((roundsAfterStageOne - 1) / 7) + 2;
        const round = ((roundsAfterStageOne - 1) % 7) + 1;

        return `${stage}-${round}`;
    }

    return {
        normalizeTraitName,
        getUnitName,
        getUnitColor,
        getUnitStyle,
        getVisibleUnitItems,
        getPlacementClass,
        getPlacementLabel,
        getLastRoundLabel,
    };
}

