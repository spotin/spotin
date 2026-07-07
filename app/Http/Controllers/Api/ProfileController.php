<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\SpotResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request): ProfileResource
    {
        return new ProfileResource($request->user());
    }

    public function update(UpdateProfileRequest $request): ProfileResource
    {
        $data = array_filter([
            'username' => $request->input('username'),
            'bio' => $request->input('bio'),
            'password' => $request->input('new_password'),
        ], fn ($value) => ! is_null($value));

        $request->user()->update($data);

        return new ProfileResource($request->user()->fresh());
    }

    public function showByUsername(string $username): JsonResponse
    {
        $user = User::where('username', $username)->firstOrFail();

        $spots = $user->spots()
            ->where('public', true)
            ->where('configured', true)
            ->get();

        return response()->json([
            'username' => $user->username,
            'bio' => $user->bio,
            'created_at' => $user->created_at,
            'spots' => SpotResource::collection($spots),
            'spots_statistics' => [
                'count' => $spots->count(),
                'latitude' => ['min' => $spots->min('latitude'), 'max' => $spots->max('latitude')],
                'longitude' => ['min' => $spots->min('longitude'), 'max' => $spots->max('longitude')],
            ],
        ]);
    }
}
