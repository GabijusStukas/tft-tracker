<?php

namespace App\Http\Controllers;

use App\Http\Requests\RiotAccountSearchRequest;
use App\Http\Resources\SummonerResource;
use App\Services\Riot\SummonerService;
use Exception;
use Illuminate\Http\JsonResponse;

class SummonerController extends Controller
{
    /**
     * @param SummonerService $summonerService
     */
    public function __construct(private SummonerService $summonerService)
    {
    }

    /**
     * @param RiotAccountSearchRequest $request
     * @return JsonResponse
     */
    public function search(RiotAccountSearchRequest $request): JsonResponse
    {
        try {
            return response()->json(new SummonerResource($this->summonerService->getSummonerByName($request->toDTO())));
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 400);
        }
    }

    /**
     * @param RiotAccountSearchRequest $request
     * @return JsonResponse
     */
    public function show(RiotAccountSearchRequest $request): JsonResponse
    {
        try {
            return response()->json($this->summonerService->getSummonerMatches($request->toDTO()));
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 400);
        }
    }
}
