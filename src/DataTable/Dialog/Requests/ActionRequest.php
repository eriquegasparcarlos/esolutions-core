<?php

namespace App\ESolutions\DataTable\Dialog\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'id' => ['required'],
            'password' => ['nullable', 'string', 'required_if:verify_password,true'],
        ];
    }
}
