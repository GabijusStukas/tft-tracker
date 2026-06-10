<?php

namespace App\Http\Controllers;

use App\Http\Requests\RiotAccountSearchRequest;
use App\Http\Resources\SummonerDetailsResource;
use App\Http\Resources\RiotAccountResource;
use App\Services\Riot\RiotService;
use Exception;
use Illuminate\Http\JsonResponse;

class RiotAccountController extends Controller
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
                new SummonerDetailsResource($this->riotService->getSummonerDetails($request->toDTO()))
            );
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 400);
        }
    }

    /**
     * @param RiotAccountSearchRequest $request
     * @return JsonResponse
     */
    public function search(RiotAccountSearchRequest $request): JsonResponse
    {
        try {
            return response()->json(new RiotAccountResource($this->riotService->getSummonerByName($request->toDTO())));
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 400);
        }
    }
}
