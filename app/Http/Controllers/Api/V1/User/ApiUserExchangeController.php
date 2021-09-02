<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\Exchange;
use Illuminate\Http\Request;

class ApiUserExchangeController extends Controller
{
    public function index()
    {
        $exchanges = Exchange::select(['name','id'])->get();

        return fractal()->collection($exchanges)->transformWith(function ($exchange) {
            return [
                'exchange_id' => $exchange->id,
                'name' => $exchange->name,
            ];
        });
    }
}
