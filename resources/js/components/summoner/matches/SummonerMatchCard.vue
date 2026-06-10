<script setup lang="ts">
import type { MatchItem } from './types';
import { formatMatchDate, normalizeTraitName } from './utils';

const props = defineProps<{ match: MatchItem }>();

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

function getTraitAccentColor(style: number): string {
    const styleColors: Record<number, string> = {
        1: '#8C5A2B',
        2: '#C0C0C0',
        3: '#FFD700',
        4: '#A855F7',
    };

    return styleColors[style] ?? '#9CA3AF';
}

function getTraitTextClass(style: number): string {
    const styleClasses: Record<number, string> = {
        1: 'text-orange-400/80',
        2: 'text-slate-300/85',
        3: 'text-yellow-200/85',
        4: 'text-violet-200/85',
    };

    return styleClasses[style] ?? 'text-slate-400/85';
}

function getTraitChipClass(): string {
    return 'border-zinc-700/80 bg-zinc-900/90';
}

function getTraitIconStyle(style: number): Record<string, string> {
    const accent = getTraitAccentColor(style);

    return {
        filter: `drop-shadow(0 0 2px ${accent})`,
    };
}
</script>

<template>
    <article class="min-h-[172px] rounded-xl border border-sidebar-border/70 bg-white p-4 dark:border-sidebar-border dark:bg-sidebar-accent">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div
                    class="h-12 w-12 rounded-lg text-center text-lg font-bold leading-[48px]"
                    :class="props.match.placement <= 4 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'"
                >
                    #{{ props.match.placement }}
                </div>
                <div class="min-w-0">
                    <div class="flex flex-wrap gap-1">
                        <span
                            v-for="(trait, traitIndex) in props.match.traits"
                            :key="`trait-${traitIndex}`"
                            class="flex items-center gap-1 rounded-full border px-2 py-0.5 text-xs font-medium"
                            :class="[getTraitChipClass(), getTraitTextClass(trait.style ?? 0)]"
                        >
                            <img
                                v-if="trait.icon"
                                :src="trait.icon"
                                :alt="trait.name ?? ''"
                                class="h-4 w-4 object-contain"
                                :style="getTraitIconStyle(trait.style ?? 0)"
                            />
                            <span>
                                {{ normalizeTraitName(trait.name ?? '') }}
                                <span class="opacity-75">{{ trait.num_units ?? 0 }}</span>
                            </span>
                        </span>
                    </div>
                    <p class="mt-2 text-xs text-muted-foreground">{{ formatMatchDate(props.match.date) }}</p>
                </div>
            </div>
        </div>

        <div class="mt-3 flex gap-2 overflow-x-auto pb-1">
            <div
                v-for="(unit, unitIndex) in props.match.units"
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
</template>

