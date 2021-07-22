<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Fractalistic\ArraySerializer;

class ApiUserController extends Controller
{
    public function __invoke(Request $request)
    {
        return fractal()->item($request->user())->transformWith(function ($user){
            return [
                'user_id' => $user->id,
                'username' => $user->id,
                'name' => $user->name,
            ];
        })
            ->serializeWith(ArraySerializer::class)
            ->toArray();
    }
}
