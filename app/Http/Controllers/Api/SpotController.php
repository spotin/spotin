<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSpotRequest;
use App\Http\Requests\UpdateSpotRequest;
use App\Http\Resources\SpotResource;
use App\Models\Spot;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SpotController extends Controller
{
    public function publicIndex(): JsonResponse
    {
        $spots = Spot::where('public', true)
            ->where('configured', true)
            ->get();

        return response()->json([
            'spots' => SpotResource::collection($spots),
            'spots_statistics' => [
                'count' => $spots->count(),
                'latitude' => [
                    'min' => $spots->min('latitude'),
                    'max' => $spots->max('latitude'),
                ],
                'longitude' => [
                    'min' => $spots->min('longitude'),
                    'max' => $spots->max('longitude'),
                ],
            ],
        ]);
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $spots = $request->user()->spots()->get();

        return SpotResource::collection($spots);
    }

    public function show(Spot $spot): SpotResource
    {
        return new SpotResource($spot);
    }

    public function store(StoreSpotRequest $request): JsonResponse
    {
        $this->authorize('create', Spot::class);

        if ($request->boolean('public')) {
            $this->authorizePublicSpot($request->user());
        }

        $spot = $request->user()->spots()->create($request->validated());

        return (new SpotResource($spot))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateSpotRequest $request, Spot $spot): SpotResource
    {
        $this->authorize('update', $spot);

        if ($request->boolean('public')) {
            $this->authorizePublicSpot($request->user());
        }

        $spot->update($request->validated());

        return new SpotResource($spot->fresh());
    }

    public function destroy(Request $request, Spot $spot): JsonResponse
    {
        $this->authorize('delete', $spot);

        $spot->delete();

        return response()->json(null, 204);
    }

    private function authorizePublicSpot(User $user): void
    {
        if (! in_array($user->role, ['CERTIFIED_USER', 'ADMIN'])) {
            abort(403, 'Standard users cannot create public spots.');
        }
    }
}
