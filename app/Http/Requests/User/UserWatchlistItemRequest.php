<?php

namespace App\Http\Requests\User;

use App\Models\Alert;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserWatchlistItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'watchlist_id' => [
                'required',
                Rule::exists('watchlists','id')->where('user_id',auth()->id())
            ],
            'exchange_id' => [
                'required',
                Rule::exists('exchanges','id')
            ],
            'symbol' => ['required'],
            'description' => [],
        ];
    }
}
