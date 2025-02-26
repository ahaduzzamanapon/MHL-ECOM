<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdministratorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool
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
            'name'                  => ['required', 'string', 'max:190'],
            'email'                 => [
                'required',
                'email',
                'max:190',
                Rule::unique("users", "email")->ignore($this->route('administrator.id'))
            ],
            'password'              => [
                $this->route('administrator.id') ? 'nullable' : 'required',
                'string',
                'min:6',
                'confirmed'
            ],
            'password_confirmation' => [$this->route('administrator.id') ? 'nullable' : 'required', 'string', 'min:6'],
            'phone'                 => [
                'nullable',
                'string',
                'regex:/^0?1[3-9]\d{8}$/',
                'not_regex:/^(\d)\1+$/',
                'regex:/^0?1(3|4|5|6|7|8|9)\d{8}$/',
                Rule::unique("users", "phone")->ignore($this->route('administrator.id'))
            ],
            'status'                => ['required', 'numeric', 'max:24'],
            'country_code'          => ['required', 'string', 'max:20'],
        ];
    }
}
