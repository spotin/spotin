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
     * The owner of the token and what the token allows, whatever its abilities.
     */
    public function __invoke(Request $request): MeResource
    {
        return new MeResource($request->user());
    }
}
