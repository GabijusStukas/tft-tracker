<?php

namespace App\Http\Controllers;

use App\Http\Requests\RiotAccountSearchRequest;
use App\Http\Resources\RiotMatchResource;
use App\Services\Riot\RiotService;
use Exception;
use Illuminate\Http\JsonResponse;

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
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 400);
        }
    }
}
