<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserWatchlistItemRequest;
use App\Models\User;
use App\Models\WatchlistItem;
use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Http\Request;
use Spatie\Fractalistic\ArraySerializer;

class ApiUserWatchlistItemController extends Controller
{
    const SORT_BY = [
        'symbol' => 'symbol'
    ];

    /**
     * Display a listing of the resource.
     *
     * @return array
     */
    public function index(Request $request)
    {
        $watchlistItems = WatchlistItem::with('exchange')
            ->where('user_id','=',auth()->id())
            ->where('watchlist_id','=',$request->input('watchlist_id'))
            ->when($request->input('term'),function ($q,$term) {
                $q->where('symbol','LIKE',"%{$term}%");
            })
            ->when($request->input('sortBy'),function ($q,$sortBy) use ($request) {
                if(!in_array($sortBy,self::SORT_BY)) return;
                /** @var QueryBuilder $q */
                $sortType = $request->input('sortType','ASC');
                $sortType = in_array($sortType,['ASC','DESC']) ? $sortType : 'ASC';
                $q->orderBy(self::SORT_BY[$sortBy],$sortType);
            })
            ->paginate();

        return [
            'items' => fractal()->collection($watchlistItems)->transformWith(function ($watchlist) {
                return [
                    'id' => $watchlist->id,
                    'symbol' => $watchlist->symbol,
                    'exchange' => [
                        'id' => $watchlist->exchange->id,
                        'name' => $watchlist->exchange->name,
                    ]
                ];
            })
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function store(UserWatchlistItemRequest $request)
    {
        WatchlistItem::create(
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
    public function show(Request $request, $id)
    {

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
    public function update(UserWatchlistItemRequest $request, $id)
    {
        $watchlist = WatchlistItem::findOrFail($id);

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
        $watchlist = WatchlistItem::findOrFail($id);
        $watchlist->delete();
    }
}
