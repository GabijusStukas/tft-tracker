<?php

namespace App\Http\Controllers;

use App\Http\Requests\RiotAccountSearchRequest;
use App\Services\Riot\SummonerService;
use Exception;
use Illuminate\Http\JsonResponse;

class SummonerMatchesController extends Controller
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
    public function index(RiotAccountSearchRequest $request): JsonResponse
    {
        try {
            return response()->json($this->summonerService->getSummonerMatches($request->toDTO()));
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 400);
        }
    }
}
