<?php

namespace App\Http\Controllers\User;

use App\Acme\Broker\Binance;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserAlertRequest;
use App\Models\Alert;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserAlertController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('user.pages.alert.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('user.pages.alert.create')->with([
            'symbols' => Binance::getSymbols()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserAlertRequest $request)
    {
        $price = Binance::getSymbolPrice($request->input('symbol'));

        if(!$price)
            throw ValidationException::withMessages(['']);

        $alert = Alert::make($request->validated());

        if(in_array($request->input('operator'),[Alert::GT,Alert::GTE, Alert::CROSS])) {
            $alert->current_position = $request->input('price') < $price ? Alert::DOWN_POSITION : Alert::UP_POSITION;
        }

        if(in_array($request->input('operator'),[Alert::LT,Alert::LTE, Alert::CROSS])) {
            $alert->current_position = $request->input('price') > $price ? Alert::UP_POSITION : Alert::DOWN_POSITION;
        }

        /** @var User $user */
        $user = auth()->user();
        $user->alerts()->save($alert);

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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        /** @var User $user */
        $user = auth()->user();
        $alert = $user->alerts()->findOrFail($id);
        return view('user.pages.alert.edit')->with([
            'alert' => $alert,
            'symbols' => Binance::getSymbols()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UserAlertRequest $request, $id)
    {
        /** @var User $user */
        $user = auth()->user();
        $alert = $user->alerts()->findOrFail($id);

        $alert->update($request->validated());

        return \Redirect::action('User\UserAlertController@edit',[$alert->getKey()]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
