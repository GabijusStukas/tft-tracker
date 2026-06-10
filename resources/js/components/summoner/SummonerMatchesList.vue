<script setup lang="ts">
import { useRiotApi } from '@/composables/useRiotApi';
import { ref, watch } from 'vue';
import SummonerMatchCard from './matches/SummonerMatchCard.vue';
import SummonerMatchSkeletonCard from './matches/SummonerMatchSkeletonCard.vue';
import type { MatchItem } from './matches/types';
import { normalizeTraitName } from './matches/utils';

interface Props {
    game: string;
    region: string;
    username: string;
    tagLine: string;
}

interface TopCompItem {
    name: string;
    games: number;
    avg: number;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    (event: 'top-comps-updated', payload: TopCompItem[]): void;
}>();

const matches = ref<MatchItem[]>([]);
const isLoading = ref(false);
const { fetchSummonerMatches } = useRiotApi();

const skeletonCards = [1, 2, 3, 4, 5];

function formatUtcTimestamp(value?: string | number): string {
    if (value === undefined || value === null || value === '') {
        return '-';
    }

    const date = typeof value === 'number' ? new Date(value) : new Date(String(value));

    if (Number.isNaN(date.getTime())) {
        return '-';
    }

    return date.toISOString();
}

async function loadMatches() {
    isLoading.value = true;

    try {
        const responseMatches = await fetchSummonerMatches({
            game: props.game,
            region: props.region,
            username: props.username,
            tagLine: props.tagLine,
        });

        matches.value = responseMatches
            .map((match: any) => {
                const participants = match?.raw_data?.info?.participants ?? [];
                const summonerParticipant = participants.find((participant: any) => participant.puuid === match?.puuid);

                if (!summonerParticipant) {
                    return null;
                }

                const placement = summonerParticipant.placement ?? 8;

                return {
                    placement,
                    traits: (summonerParticipant.traits ?? [])
                        .filter((trait: any) => (trait.style ?? 0) > 0)
                        .sort((a: any, b: any) => (b.style ?? 0) - (a.style ?? 0) || (b.num_units ?? 0) - (a.num_units ?? 0))
                        .map((trait: any) => ({
                            icon: trait.icon ?? null,
                            name: trait.name ?? null,
                            style: trait.style ?? 0,
                            num_units: trait.num_units ?? 0,
                        })),
                    gameType: String(match?.raw_data?.info?.queue_name ?? ''),
                    date: formatUtcTimestamp(match?.match_created_at ?? match?.raw_data?.info?.game_datetime ?? match?.raw_data?.info?.gameCreation),
                    units: (summonerParticipant.units ?? []).map((unit: any) => ({
                        character_id: unit.character_id,
                        name: unit.name ?? null,
                        icon: unit.icon ?? null,
                        rarity: Number(unit.rarity ?? 0),
                        tier: Number(unit.tier ?? 0),
                        items: (unit.items ?? []).slice(0, 3).map((item: any) => ({
                            icon: item.icon ?? null,
                        })),
                    })),
                };
            })
            .filter(Boolean) as MatchItem[];

        const compStats = new Map<string, { games: number; totalPlacement: number }>();

        for (const match of matches.value) {
            const compName =
                match.traits
                    .slice(0, 2)
                    .map((t) => normalizeTraitName(t.name ?? 'Unknown'))
                    .join(' / ') || 'Unknown comp';
            const current = compStats.get(compName) ?? { games: 0, totalPlacement: 0 };
            current.games += 1;
            current.totalPlacement += match.placement;
            compStats.set(compName, current);
        }

        const topComps = Array.from(compStats.entries())
            .map(([name, stat]) => ({
                name,
                games: stat.games,
                avg: Number((stat.totalPlacement / stat.games).toFixed(2)),
            }))
            .sort((a, b) => b.games - a.games || a.avg - b.avg)
            .slice(0, 5);

        emit('top-comps-updated', topComps);
    } finally {
        isLoading.value = false;
    }
}

watch(
    () => [props.game, props.region, props.username, props.tagLine],
    () => {
        void loadMatches();
    },
    { immediate: true },
);
</script>

<template>
    <div class="flex flex-col gap-4">
        <SummonerMatchSkeletonCard
            v-if="isLoading"
            v-for="card in skeletonCards"
            :key="`skeleton-${card}`"
        />

        <SummonerMatchCard
            v-else-if="matches.length > 0"
            v-for="(match, index) in matches"
            :key="`${match.date}-${index}`"
            :match="match"
        />

        <article
            v-else
            class="rounded-xl border border-dashed border-sidebar-border/70 bg-white p-6 text-sm text-muted-foreground dark:border-sidebar-border dark:bg-sidebar-accent"
        >
            No recent matches found.
        </article>
    </div>
</template>
