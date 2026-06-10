<script setup lang="ts">
interface MatchItem {
    placement: number;
    comp: string;
    lp: string;
    date: string;
    units: Array<{
        character_id: string;
        name?: string | null;
        icon?: string | null;
        rarity: number;
        tier: number;
    }>;
}

const props = defineProps<Props>();

interface Props {
    matches: MatchItem[];
}

function getUnitName(characterId: string): string {
    return characterId.replace(/^TFT\d+_/, '');
}

function getUnitColor(rarity: number): string {
    const rarityColors: Record<number, string> = {
        6: '#EB9C00',
        4: '#E537A2',
        2: '#0093FF',
        1: '#00AE0A',
        0: '#9AA4AF',
    };

    return rarityColors[rarity] ?? '#9AA4AF';
}

function getUnitStyle(rarity: number): Record<string, string> {
    return {
        borderColor: getUnitColor(rarity),
    };
}

function formatMatchDate(date: string): string {
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
</script>

<template>
    <div class="flex flex-col gap-4">
        <article
            v-for="(match, index) in props.matches"
            :key="`${match.comp}-${index}`"
            class="rounded-xl border border-sidebar-border/70 bg-white p-4 dark:border-sidebar-border dark:bg-sidebar-accent"
        >
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div
                        class="h-12 w-12 rounded-lg text-center text-lg font-bold leading-[48px]"
                        :class="match.placement <= 4 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'"
                    >
                        #{{ match.placement }}
                    </div>
                    <div>
                        <p class="font-semibold text-sidebar-foreground">{{ match.comp }}</p>
                        <p class="text-xs text-muted-foreground">{{ formatMatchDate(match.date) }}</p>
                    </div>
                </div>
                <p class="text-sm font-semibold" :class="match.lp.startsWith('+') ? 'text-emerald-500' : 'text-rose-500'">{{ match.lp }}</p>
            </div>

            <div class="mt-3 flex gap-2 overflow-x-auto pb-1">
                <div
                    v-for="(unit, unitIndex) in match.units"
                    :key="`${unit.character_id}-${unitIndex}`"
                    class="flex w-16 shrink-0 flex-col items-center gap-1"
                >
                    <div class="min-h-3 text-[10px] leading-none" :style="{ color: getUnitColor(unit.rarity) }">
                        <span v-for="starIndex in unit.tier" :key="`${unit.character_id}-star-${starIndex}`">★</span>
                    </div>
                    <img
                        v-if="unit.icon"
                        :src="unit.icon"
                        :alt="unit.name ?? getUnitName(unit.character_id)"
                        class="h-14 w-14 rounded-md border-2 object-cover"
                        :style="getUnitStyle(unit.rarity)"
                    />
                    <div
                        v-else
                        class="flex h-14 w-14 items-center justify-center rounded-md border-2 bg-muted text-[10px] text-muted-foreground"
                        :style="getUnitStyle(unit.rarity)"
                    >
                        {{ getUnitName(unit.character_id).slice(0, 3) }}
                    </div>
                    <p class="w-full truncate text-center text-[10px] leading-tight text-muted-foreground">
                        {{ unit.name ?? getUnitName(unit.character_id) }}
                    </p>
                </div>
            </div>
        </article>
    </div>
</template>
