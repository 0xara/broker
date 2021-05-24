<?php

namespace App\Http\Requests\User;

use App\Models\Alert;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserAlertRequest extends FormRequest
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
        $this->merge([
            'active' => $this->input('active') ? 1 : 0
        ]);

        return [
            'broker_id' => [
                'required',
                Rule::exists('brokers','id')
            ],
            'symbol' => ['required'],
            'operator' => [
                'required',
                Rule::in(array_keys(Alert::OPERATOR_TITLES))
            ],
            'price' => [
                'required'
            ],
            'active' => Rule::in([0,1])
        ];
    }
}
