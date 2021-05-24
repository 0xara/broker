<?php

namespace App\Http\Requests;

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
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if($this->input('operator') != Alert::BETWEEN) {
            $this->request->remove("max");
            $this->request->remove("min");
        }
        else{
            $this->request->remove("price");
        }

        return [
            'exchange_id' => [
                'required',
                Rule::exists('exchanges','id')
            ],
            'symbol' => ['required'],
            'operator' => [
                'required',
                Rule::in(array_keys(Alert::OPERATOR_TITLES))
            ],
            'price' => [
                Rule::requiredIf(function () {
                    return $this->input('Operator') != Alert::BETWEEN;
                })
            ],
            'max' => [
                Rule::requiredIf(function () {
                    return $this->input('Operator') == Alert::BETWEEN;
                }),
                'numeric',
            ],
            'min' => [
                Rule::requiredIf(function () {
                    return $this->input('Operator') == Alert::BETWEEN;
                }),
                'numeric'
            ]
        ];
    }
}
