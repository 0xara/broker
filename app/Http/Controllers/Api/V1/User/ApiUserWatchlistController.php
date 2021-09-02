<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserWatchlistRequest;
use App\Models\Watchlist;
use Illuminate\Http\Request;
use Spatie\Fractalistic\ArraySerializer;

class ApiUserWatchlistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return array
     */
    public function index()
    {
        return [
            'watchlists' => fractal()->collection(auth()->user()->watchlists)->transformWith(function ($watchlist) {
                return [
                    'id' => $watchlist->id,
                    'name' => $watchlist->name,
                ];
            })
                ->serializeWith(ArraySerializer::class)
                ->toArray()
        ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function store(UserWatchlistRequest $request)
    {
        Watchlist::create(
            $request->validated()
        );
        return [];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return array
     */
    public function update(UserWatchlistRequest $request, $id)
    {
        $watchlist = Watchlist::findOrFail($id);

        $watchlist->update(
            $request->validated()
        );
        return [];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $watchlist = Watchlist::findOrFail($id);
        $watchlist->delete();
    }
}
