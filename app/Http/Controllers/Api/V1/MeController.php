<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\MeResource;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;

#[Group('Me')]
class MeController extends Controller
{
    /**
     * Get the user of the token.
     */
    public function __invoke(Request $request): MeResource
    {
        return new MeResource($request->user());
    }
}
