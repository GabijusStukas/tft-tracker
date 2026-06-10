<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import SummonerMatchesList from '@/components/summoner/SummonerMatchesList.vue';
import SummonerProfileCard from '@/components/summoner/SummonerProfileCard.vue';
import SummonerSidebar from '@/components/summoner/SummonerSidebar.vue';
import SummonerTabs from '@/components/summoner/SummonerTabs.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

interface Props {
	game: string;
	region: string;
	username: string;
	tagLine: string;
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

const recentMatches = [
	{ placement: 2, comp: 'Vertical Faerie', lp: '+42 LP', date: '2h ago' },
	{ placement: 7, comp: 'Invoker Flex', lp: '-31 LP', date: '4h ago' },
	{ placement: 1, comp: 'Fated Re-roll', lp: '+56 LP', date: '7h ago' },
	{ placement: 5, comp: 'Umbral Duelist', lp: '-12 LP', date: '1d ago' },
];

const topComps = [
	{ name: 'Faerie Flex', games: 18, avg: 3.7 },
	{ name: 'Invoker Duo Carry', games: 12, avg: 4.1 },
	{ name: 'Fortune Pivot', games: 9, avg: 4.6 },
];
</script>

<template>
	<Head title="Summoner" />

	<AppLayout :breadcrumbs="breadcrumbs">
		<div class="min-h-full bg-[#f3f5f8] p-4 dark:bg-sidebar">
			<div class="mx-auto flex w-full max-w-7xl flex-col gap-4">
				<SummonerProfileCard :username="props.username" :tag-line="props.tagLine" :region="props.region" />
				<SummonerTabs />

				<section class="grid gap-4 lg:grid-cols-[minmax(0,1.75fr)_minmax(0,1fr)]">
					<SummonerMatchesList :matches="recentMatches" />
					<SummonerSidebar :top-comps="topComps" />
				</section>
			</div>
		</div>
	</AppLayout>
</template>

