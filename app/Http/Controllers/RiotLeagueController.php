<?php

namespace App\Http\Controllers;

use App\Http\Requests\RiotAccountSearchRequest;
use App\Http\Resources\RiotLeagueResource;
use App\Services\Riot\RiotService;
use Exception;
use Illuminate\Http\JsonResponse;

class RiotLeagueController extends Controller
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
                RiotLeagueResource::collection($this->riotService->getAccountLeague($request->toDTO()))
            );
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 400);
        }
    }
}
