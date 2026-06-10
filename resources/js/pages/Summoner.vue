<script setup lang="ts">
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import SummonerMatchesList from '@/components/summoner/SummonerMatchesList.vue';
import SummonerProfileCard from '@/components/summoner/SummonerProfileCard.vue';
import SummonerSidebar from '@/components/summoner/SummonerSidebar.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

interface Props {
	game: string;
	region: string;
	username: string;
	tagLine: string;
}

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

interface TopCompItem {
	name: string;
	games: number;
	avg: number;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
	{
		title: 'Dashboard',
		href: '/dashboard',
	},
	{
		title: 'Summoner',
		href: `/${props.game}/summoners/${props.region}/${props.username}-${props.tagLine}`,
	},
];

const recentMatches = ref<MatchItem[]>([]);
const topComps = ref<TopCompItem[]>([]);

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

function normalizeTraitName(name: string): string {
	return name
		.replace(/^TFT\d+_/, '')
		.replace(/(UniqueTrait|Trait)$/, '')
		.replace(/_/g, ' ');
}

function getCompName(traits: Array<{ name?: string; style?: number; num_units?: number }>): string {
	const activeTraits = traits
		.filter((trait) => (trait.style ?? 0) > 0)
		.sort((a, b) => (b.style ?? 0) - (a.style ?? 0) || (b.num_units ?? 0) - (a.num_units ?? 0))
		.slice(0, 2);

	if (activeTraits.length === 0) {
		return 'Unknown comp';
	}

	return activeTraits.map((trait) => normalizeTraitName(trait.name ?? 'Unknown')).join(' / ');
}

async function loadMatches() {
	const response = await axios.get(
		route('riot.matches', {
			game: props.game,
			region: props.region,
			username: props.username,
			tag_line: props.tagLine,
		}),
	);

	const matches = Array.isArray(response.data) ? response.data : response.data?.data ?? [];

	recentMatches.value = matches
		.map((match: any) => {
			const participants = match?.raw_data?.info?.participants ?? [];
			// Resolve the exact summoner row from the match payload by puuid.
			const summonerParticipant = participants.find((participant: any) => participant.puuid === match?.puuid);

			if (!summonerParticipant) {
				return null;
			}

			const placement = summonerParticipant.placement ?? 8;
			const damage = summonerParticipant.total_damage_to_players ?? 0;

			return {
				placement,
				comp: getCompName(summonerParticipant.traits ?? []),
				lp: `${placement <= 4 ? '+' : '-'}${damage} DMG`,
				date: formatUtcTimestamp(match?.match_created_at ?? match?.raw_data?.info?.game_datetime ?? match?.raw_data?.info?.gameCreation),
				units: (summonerParticipant.units ?? []).map((unit: any) => ({
					character_id: unit.character_id,
					name: unit.name ?? null,
					icon: unit.icon ?? null,
					rarity: Number(unit.rarity ?? 0),
					tier: Number(unit.tier ?? 0),
				})),
			};
		})
		.filter(Boolean) as MatchItem[];

	const compStats = new Map<string, { games: number; totalPlacement: number }>();

	for (const match of recentMatches.value) {
		const current = compStats.get(match.comp) ?? { games: 0, totalPlacement: 0 };
		current.games += 1;
		current.totalPlacement += match.placement;
		compStats.set(match.comp, current);
	}

	topComps.value = Array.from(compStats.entries())
		.map(([name, stat]) => ({
			name,
			games: stat.games,
			avg: Number((stat.totalPlacement / stat.games).toFixed(2)),
		}))
		.sort((a, b) => b.games - a.games || a.avg - b.avg)
		.slice(0, 5);
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
	<Head title="Summoner" />

	<AppLayout :breadcrumbs="breadcrumbs">
		<div class="min-h-full bg-[#f3f5f8] p-4 dark:bg-sidebar">
			<div class="mx-auto flex w-full max-w-7xl flex-col gap-4">
				<SummonerProfileCard :game="props.game" :username="props.username" :tag-line="props.tagLine" :region="props.region" />

				<section class="grid gap-4 lg:grid-cols-[minmax(0,1.75fr)_minmax(0,1fr)]">
					<SummonerMatchesList :matches="recentMatches" />
					<SummonerSidebar :top-comps="topComps" />
				</section>
			</div>
		</div>
	</AppLayout>
</template>

