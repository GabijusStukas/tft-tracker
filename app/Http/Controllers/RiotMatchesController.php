<?php

namespace App\Http\Controllers;

use App\Http\Requests\RiotAccountSearchRequest;
use App\Http\Resources\RiotMatchResource;
use App\Services\Riot\RiotService;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Throwable;

class RiotMatchesController extends Controller
{
    /**
     * @param RiotService $riotService
     */
    public function __construct(private RiotService $riotService)
    {
    }

    /**
     * @param RiotAccountSearchRequest $request
     * @return JsonResponse
     */
    public function index(RiotAccountSearchRequest $request): JsonResponse
    {
        try {
            return response()->json(
                RiotMatchResource::collection($this->riotService->getSummonerMatches($request->toDTO()))
            );
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 400);
        }
    }

    /**
     * @param string $matchId
     * @return JsonResponse
     */
    public function show(string $matchId): JsonResponse
    {
        try {
            return response()->json(
                new RiotMatchResource($this->riotService->getMatchDetails($matchId))
            );
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 400);
        }
    }
}
