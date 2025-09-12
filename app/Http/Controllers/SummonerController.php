<?php

namespace App\Http\Controllers;

use App\Http\Requests\SummonerSearchRequest;
use App\Services\Riot\SummonerService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;

class SummonerController extends Controller
{
    /**
     * @param SummonerSearchRequest $request
     * @param SummonerService $summonerService
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function show(SummonerSearchRequest $request, SummonerService $summonerService): JsonResponse
    {
        try {
            $summoner = $summonerService->getSummonerByName($request->toDTO());
            return response()->json($summoner);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
