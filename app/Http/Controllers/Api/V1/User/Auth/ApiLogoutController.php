<?php

namespace App\Http\Controllers\Api\V1\User\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiLogoutController extends Controller
{
    /**
     * @param Request $request
     * @return array
     */
    public function __invoke(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return [];
    }
}
