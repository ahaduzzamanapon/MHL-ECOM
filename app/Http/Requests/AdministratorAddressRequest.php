<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdministratorAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'full_name'    => ['required', 'string', 'max:190'],
            'email'        => ['nullable', 'string', 'max:190'],
            'country_code' => ['required', 'string', 'max:28'],
             'phone'       => ['required', 'string', 
                               'regex:/^01[3-9]\d{8}$/', 
                               'not_regex:/^(\d)\1+$/', 
                               'regex:/^01(3|4|5|6|7|8|9)\d{8}$/'],
            'country'      => ['required', 'string', 'max:100'],
            'state'        => ['nullable', 'string', 'max:100'],
            'city'         => ['nullable', 'string', 'max:100'],
            'zip_code'     => ['nullable', 'string'],
            'address'      => ['required', 'string', 'max:500'],
        ];
    }
}
