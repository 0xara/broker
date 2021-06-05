<?php

namespace App\Http\Controllers\Api\V1\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ApiLoginController extends Controller
{
    /**
     * @param Request $request
     * @return \Laravel\Sanctum\string|string
     * @throws ValidationException
     */
    public function __invoke(Request $request)
    {
        //dd($request->all());

        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'device_name' => 'string',
        ]);

        $user = User::where('username', $request->input('username'))->first();

        if (! $user || ! Hash::check($request->password, $user->password))
            throw ValidationException::withMessages(['username' => ['The provided credentials are incorrect.'],]);

        return $user->createToken(/*$request->device_name*/$request->username)->plainTextToken;
    }
}
