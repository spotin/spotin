<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * The owner of the token and what the token allows, whatever its abilities.
     */
    public function __invoke(Request $request): UserResource
    {
        return new UserResource($request->user());
    }
}
