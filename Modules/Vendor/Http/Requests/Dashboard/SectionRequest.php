<?php

namespace Modules\Vendor\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class SectionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->getMethod()) {
            // handle creates
            case 'post':
            case 'POST':

                return [
//                  'title.*'         => 'required',
                    'title.*' => 'required|unique:section_translations,title',
                    'description.*' => 'nullable',
                    'image' => 'nullable',
                ];

            //handle updates
            case 'put':
            case 'PUT':
                return [
//                    'title.*'          => 'required',
                    'title.*' => 'required|unique:section_translations,title,' . $this->id . ',section_id',
                    'description.*' => 'nullable',
                    'image' => 'nullable',
                ];
        }
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function messages()
    {

        foreach (config('laravellocalization.supportedLocales') as $key => $value) {

            $v["title." . $key . ".required"] = __('vendor::dashboard.sections.validation.title.required') . ' - ' . $value['native'] . '';

            $v["title." . $key . ".unique"] = __('vendor::dashboard.sections.validation.title.unique') . ' - ' . $value['native'] . '';

            $v["description." . $key . ".required"] = __('vendor::dashboard.sections.validation.description.required') . ' - ' . $value['native'] . '';

        }

        return $v;

    }
}
