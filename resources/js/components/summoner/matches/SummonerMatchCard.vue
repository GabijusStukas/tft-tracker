<script setup lang="ts">
import { useTftMatchPresentation } from '@/composables/useTftMatchPresentation';
import TraitBadge from '../../traits/TraitBadge.vue';
import type { MatchItem } from './types';
import { formatMatchDate } from './utils';

const props = defineProps<{ match: MatchItem }>();

const { normalizeTraitName, getUnitName, getUnitColor, getUnitStyle, getVisibleUnitItems, getPlacementClass, getPlacementLabel } = useTftMatchPresentation();

function getMatchDetailsHref(matchId: string): string {
    return `/tft/match/${encodeURIComponent(matchId)}`;
}
</script>

<template>
    <article class="min-h-[172px] rounded-xl border border-sidebar-border/70 bg-white p-4 dark:border-sidebar-border dark:bg-sidebar-accent">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div
                    class="flex h-16 w-16 shrink-0 flex-col items-center justify-center rounded-xl text-center font-bold"
                    :class="getPlacementClass(props.match.placement)"
                >
                    <span class="text-xl leading-none">#{{ props.match.placement }}</span>
                    <span class="mt-0.5 text-[9px] font-semibold uppercase leading-none opacity-85">
                        {{ getPlacementLabel(props.match.placement) }}
                    </span>
                </div>
                <div class="min-w-0">
                    <div class="flex flex-wrap gap-2">
                        <TraitBadge
                            v-for="(trait, traitIndex) in props.match.traits"
                            :key="`trait-${traitIndex}`"
                            :trait="trait"
                            :label="normalizeTraitName(trait.name ?? '')"
                        />
                    </div>
                    <p class="mt-2 flex items-center gap-2 text-xs text-muted-foreground">
                        <span>{{ formatMatchDate(props.match.date) }}</span>
                        <span class="opacity-60">•</span>
                        <span class="font-semibold uppercase tracking-wide">{{ props.match.gameType || '-' }}</span>
                    </p>
                </div>
            </div>
            <a
                :href="getMatchDetailsHref(props.match.matchId)"
                class="shrink-0 rounded-md border border-blue-300/70 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 transition hover:bg-blue-100 dark:border-blue-500/40 dark:bg-blue-500/10 dark:text-blue-200 dark:hover:bg-blue-500/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/50"
            >
                View Match
            </a>
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
                <div v-if="(unit.items ?? []).length > 0" class="mt-0.5 flex items-center justify-center gap-0.5">
                    <div
                        v-for="(item, itemIndex) in getVisibleUnitItems(unit.items ?? [])"
                        :key="`${unit.character_id}-item-${itemIndex}`"
                        class="h-4 w-4 overflow-hidden rounded-[3px] border border-sidebar-border/60 bg-muted/60"
                    >
                        <img :src="item.icon ?? ''" alt="Item" class="h-full w-full object-cover" />
                    </div>
                </div>
            </div>
        </div>
    </article>
</template>
