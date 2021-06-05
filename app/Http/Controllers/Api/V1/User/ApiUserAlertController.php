<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Acme\Broker\Binance;
use App\Acme\CarbonFa\CarbonFa;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserAlertRequest;
use App\Models\Alert;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ApiUserAlertController extends Controller
{
    const SORT = [
        'create' => 'created_at',
        'update' => 'updated_at',
        'symbol' => 'symbol',
        'broker' => 'broker',
        'active' => 'active',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return array
     */
    public function index(Request $request)
    {
        $alerts =  Alert::with('broker')->when($request->input('sortBy'),function ($q, $val) {
            if(in_array($val,array_keys(self::SORT))) /** @var Alert $q */ $q->orderBy(self::SORT[$val]);
        })->paginate();

        return [
            'alerts' =>fractal($alerts)
                ->transformWith(function ($alert) {
                    return [
                        'id' => $alert->id,
                        'symbol' => $alert->symbol,
                        'broker' => ['name' => $alert->broker->name],
                        'operator' => $alert->operator,
                        'price' => (float) $alert->price,
                        'active' => $alert->active,
                        'repeat' => $alert->repeat,
                        'details' => $alert->details,
                        'created_at' => CarbonFa::setCarbon($alert->created_at)->toJalali(true,'Y/m/d H:i'),
                        'updated_at' => CarbonFa::setCarbon($alert->updated_at)->toJalali(true,'Y/m/d H:i'),
                    ];
                })->toArray()
        ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        $symbols = Binance::getSymbols();

/*        if(!$symbols->contains($request->old('symbol'))) {
            $request->flashOnly('symbol');
        }*/

        if($request->wantsJson()) {
            return [
                'symbols' => $this->prepareSymbols($symbols),
                'operator_titles' => Alert::OPERATOR_TITLES
            ];
        }

        return view('user.pages.alert.create')->with([
            'symbols' => $this->prepareSymbols($symbols)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Http\RedirectResponse
     */
    public function store(UserAlertRequest $request)
    {
        $price = Binance::getSymbolPrice($request->input('symbol'));

        if(!$price)
            throw ValidationException::withMessages(['']);

        $alert = Alert::make(
            array_merge(
                $request->validated(),
                [
                    'active' => $request->input('active') == 1 ? 1 : 0,
                    'repeat' => $request->input('repeat') == 1 ? 1 : 0,
                    'charts' => $request->input('charts') ?: [],
                ]
            )
        );

        if(in_array($request->input('operator'),[Alert::GT,Alert::GTE, Alert::CROSS])) {
            $alert->current_position = $request->input('price') < $price ? Alert::DOWN_POSITION : Alert::UP_POSITION;
        }

        if(in_array($request->input('operator'),[Alert::LT,Alert::LTE, Alert::CROSS])) {
            $alert->current_position = $request->input('price') > $price ? Alert::UP_POSITION : Alert::DOWN_POSITION;
        }

        /** @var User $user */
        $user = auth()->user();
        $user->alerts()->save($alert);


        if($request->wantsJson()) {
            return [
                'alert' => [
                    'id' => $alert->getKey()
                ]
            ];
        }

        return \Redirect::action('User\UserAlertController@edit',[$alert->getKey()]);
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
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        /** @var User $user */
        $user = auth()->user();
        $alert = $user->alerts()->findOrFail($id);

        $symbols = Binance::getSymbols();

        if($request->wantsJson()) {
            return [
                'alert' => fractal()->item($alert)->transformWith(function ($alert) {
                    /** @var Alert $alert */
                    return array_merge($alert->toArray(), ['price' => (float) $alert->price]);
                })->toArray(),
                'symbols' => $this->prepareSymbols($symbols),
                'operator_titles' => Alert::OPERATOR_TITLES
            ];
        }

/*        if(!$symbols->contains($request->old('symbol'))) {
            $request->flashOnly('symbol');
        }*/

        return view('user.pages.alert.edit')->with([
            'alert' => $alert,
            'symbols' => $this->prepareSymbols($symbols),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return array|\Illuminate\Http\RedirectResponse
     */
    public function update(UserAlertRequest $request, $id)
    {
        /** @var User $user */
        $user = auth()->user();
        $alert = $user->alerts()->findOrFail($id);

        $price = Binance::getSymbolPrice($request->input('symbol'));

        if(!$price)
            throw ValidationException::withMessages(['']);

        if(in_array($request->input('operator'),[Alert::GT,Alert::GTE, Alert::CROSS])) {
            $alert->current_position = $request->input('price') < $price ? Alert::UP_POSITION : Alert::DOWN_POSITION;
        }

        if(in_array($request->input('operator'),[Alert::LT,Alert::LTE, Alert::CROSS])) {
            $alert->current_position = $request->input('price') > $price ? Alert::DOWN_POSITION : Alert::UP_POSITION;
        }

        $alert->update(array_merge($request->validated(), [
                'active' => $request->input('active') == 1 ? 1 : 0,
                'repeat' => $request->input('repeat') == 1 ? 1 : 0,
                'charts' => $request->input('charts') ?: [],
            ]
        ));

        if($request->wantsJson()) {
            return [
                'alert' => [
                    'id' => $alert->getKey()
                ]
            ];
        }

        return \Redirect::action('User\UserAlertController@edit',[$alert->getKey()]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        /** @var User $user */
        $user = auth()->user();
        $alert = $user->alerts()->findOrFail($id);

        $alert->delete();

        return back();
    }

    /**
     * @param $symbols
     * @return array
     */
    public function prepareSymbols($symbols)
    {
        $result = [];

        foreach ($symbols as $symbol) {
            if(!array_key_exists($symbol['quoteAsset'],$result)) {
                $result[$symbol['quoteAsset']] = [];
            }
            $result[$symbol['quoteAsset']][] = $symbol['symbol'];
        }
        return $result;
    }
}
