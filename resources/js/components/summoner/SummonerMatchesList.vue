<script setup lang="ts">
import { useRiotApi } from '@/composables/useRiotApi';
import { ref, watch } from 'vue';
import SummonerMatchCard from './matches/SummonerMatchCard.vue';
import SummonerMatchSkeletonCard from './matches/SummonerMatchSkeletonCard.vue';
import type { MatchItem } from './matches/types';

interface Props {
    game: string;
    region: string;
    username: string;
    tagLine: string;
    refreshToken: number;
}


const props = defineProps<Props>();


const matches = ref<MatchItem[]>([]);
const isLoading = ref(false);
const { fetchSummonerMatches } = useRiotApi();

const skeletonCards = [1, 2, 3, 4, 5];

function normalizeText(value?: string | null): string {
    return String(value ?? '').trim().toLowerCase();
}

function normalizeTagLine(value?: string | null): string {
    return normalizeText(value).replace(/^#/, '');
}

function normalizeIsoTimestamp(value?: string | number): string | number | undefined {
    if (typeof value !== 'string') {
        return value;
    }

    // Laravel serializes microseconds (e.g. .000000Z); trim to milliseconds for stable Date parsing.
    return value.replace(/\.(\d{3})\d+Z$/, '.$1Z');
}

function asArray<T>(value: unknown): T[] {
    if (Array.isArray(value)) {
        return value as T[];
    }

    if (value && typeof value === 'object') {
        return Object.values(value as Record<string, T>);
    }

    return [];
}

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

async function loadMatches(refresh = false) {
    isLoading.value = true;

    try {
        const responseMatches = await fetchSummonerMatches({
            game: props.game,
            region: props.region,
            username: props.username,
            tagLine: props.tagLine,
        }, { refresh });

        matches.value = asArray<any>(responseMatches)
            .map((match: any) => {
                const participants = asArray<any>(match?.participants);
                const expectedGameName = normalizeText(props.username);
                const expectedTagLine = normalizeTagLine(props.tagLine);
                const summonerParticipant = participants.find((participant: any) => {
                    return normalizeText(participant?.game_name) === expectedGameName
                        && normalizeTagLine(participant?.tag_line) === expectedTagLine;
                }) ?? participants[0];

                if (!summonerParticipant) {
                    return null;
                }

                const placement = summonerParticipant.placement ?? 8;

                return {
                    matchId: String(match?.match_id ?? ''),
                    placement,
                    traits: asArray<any>(summonerParticipant?.traits)
                        .filter((trait: any) => (trait.style ?? 0) > 0)
                        .sort((a: any, b: any) => (b.style ?? 0) - (a.style ?? 0) || (b.num_units ?? 0) - (a.num_units ?? 0))
                        .map((trait: any) => ({
                            icon: trait.icon ?? null,
                            name: trait.name ?? null,
                            style: trait.style ?? 0,
                            num_units: trait.num_units ?? 0,
                        })),
                    gameType: String(match?.queue_name ?? ''),
                    date: formatUtcTimestamp(normalizeIsoTimestamp(match?.match_created_at)),
                    units: asArray<any>(summonerParticipant?.units).map((unit: any, unitIndex: number) => ({
                        character_id: unit.character_id ?? unit.name ?? `unit-${unitIndex}`,
                        name: unit.name ?? null,
                        icon: unit.icon ?? null,
                        rarity: Number(unit.rarity ?? 0),
                        tier: Number(unit.tier ?? 0),
                        items: asArray<any>(unit?.items).slice(0, 3).map((item: any) => ({
                            icon: item.icon ?? null,
                        })),
                    })),
                };
            })
            .filter(Boolean) as MatchItem[];
    } catch (error) {
        console.error('Failed to load matches', error);
        matches.value = [];
    } finally {
        isLoading.value = false;
    }
}

watch(
    () => [props.game, props.region, props.username, props.tagLine],
    () => {
        void loadMatches(false);
    },
    { immediate: true },
);

watch(
    () => props.refreshToken,
    (value) => {
        if (value > 0) {
            void loadMatches(true);
        }
    },
);
</script>

<template>
    <div class="flex flex-col gap-4">
        <template v-if="isLoading">
            <SummonerMatchSkeletonCard
                v-for="card in skeletonCards"
                :key="`skeleton-${card}`"
            />
        </template>

        <template v-else-if="matches.length > 0">
            <SummonerMatchCard
                v-for="(match, index) in matches"
                :key="`${match.matchId}-${index}`"
                :match="match"
            />
        </template>

        <article
            v-else
            class="rounded-xl border border-dashed border-sidebar-border/70 bg-white p-6 text-sm text-muted-foreground dark:border-sidebar-border dark:bg-sidebar-accent"
        >
            No recent matches found.
        </article>
    </div>
</template>
