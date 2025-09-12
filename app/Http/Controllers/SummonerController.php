<?php

namespace App\Http\Controllers;

use App\Http\Requests\SummonerSearchRequest;
use App\Http\Resources\SummonerResource;
use App\Services\Riot\SummonerService;
use Exception;
use Illuminate\Http\JsonResponse;

class SummonerController extends Controller
{
    /**
     * @param SummonerSearchRequest $request
     * @param SummonerService $summonerService
     * @return JsonResponse
     */
    public function show(SummonerSearchRequest $request, SummonerService $summonerService): JsonResponse
    {
        try {
            return response()->json(new SummonerResource($summonerService->getSummonerByName($request->toDTO())));
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 400);
        }
    }
}
