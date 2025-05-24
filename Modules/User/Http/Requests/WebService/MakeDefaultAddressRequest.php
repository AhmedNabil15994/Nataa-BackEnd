<?php

namespace Modules\User\Http\Requests\WebService;

use Illuminate\Foundation\Http\FormRequest;

class MakeDefaultAddressRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'address_id' => 'required|exists:addresses,id',
        ];
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [];
    }
}
