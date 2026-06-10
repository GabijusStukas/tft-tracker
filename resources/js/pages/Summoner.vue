<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import SummonerMatchesList from '@/components/summoner/SummonerMatchesList.vue';
import SummonerProfileCard from '@/components/summoner/SummonerProfileCard.vue';
import SummonerSidebar from '@/components/summoner/SummonerSidebar.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

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

const topComps = ref<TopCompItem[]>([]);
function updateTopComps(value: TopCompItem[]): void {
	topComps.value = value;
}
</script>

<template>
	<Head title="Summoner" />

	<AppLayout :breadcrumbs="breadcrumbs">
		<div class="min-h-full bg-[#f3f5f8] p-4 dark:bg-sidebar">
			<div class="mx-auto flex w-full max-w-7xl flex-col gap-4">
				<SummonerProfileCard :game="props.game" :username="props.username" :tag-line="props.tagLine" :region="props.region" />

				<section class="grid gap-4 lg:grid-cols-[minmax(0,1.75fr)_minmax(0,1fr)]">
					<SummonerMatchesList
						:game="props.game"
						:region="props.region"
						:username="props.username"
						:tag-line="props.tagLine"
						@top-comps-updated="updateTopComps"
					/>
					<SummonerSidebar :top-comps="topComps" />
				</section>
			</div>
		</div>
	</AppLayout>
</template>

