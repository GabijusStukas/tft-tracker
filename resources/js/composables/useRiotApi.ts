import axios from 'axios';

interface RiotAccountRouteParams {
    game: string;
    region: string;
    username: string;
    tagLine: string;
}

interface RiotRequestOptions {
    refresh?: boolean;
}

export function useRiotApi() {
    async function searchRiotAccount(username: string, tagLine: string): Promise<any> {
        const response = await axios.get(route('riot.account.search'), {
            params: {
                username,
                tag_line: tagLine,
            },
        });

        return response.data?.data ?? response.data;
    }

    async function fetchSummonerProfile(params: RiotAccountRouteParams, options?: RiotRequestOptions): Promise<any> {
        const response = await axios.get(
            route('riot.account.index', {
                game: params.game,
                region: params.region,
                username: params.username,
                tag_line: params.tagLine,
            }),
            {
                params: options?.refresh ? { refresh: 1 } : {},
            },
        );

        return response.data ?? {};
    }

    async function fetchSummonerMatches(params: RiotAccountRouteParams, options?: RiotRequestOptions): Promise<any[]> {
        const response = await axios.get(
            route('riot.matches', {
                game: params.game,
                region: params.region,
                username: params.username,
                tag_line: params.tagLine,
            }),
            {
                params: options?.refresh ? { refresh: 1 } : {},
            },
        );

        return Array.isArray(response.data) ? response.data : response.data?.data ?? [];
    }

    async function fetchSummonerLeague(params: RiotAccountRouteParams, options?: RiotRequestOptions): Promise<any[]> {
        const response = await axios.get(
            route('riot.league', {
                game: params.game,
                region: params.region,
                username: params.username,
                tag_line: params.tagLine,
            }),
            {
                params: options?.refresh ? { refresh: 1 } : {},
            },
        );

        return Array.isArray(response.data) ? response.data : response.data?.data ?? [];
    }

    return {
        searchRiotAccount,
        fetchSummonerProfile,
        fetchSummonerMatches,
        fetchSummonerLeague,
    };
}

