<?php

namespace Modules\Cart\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Catalog\Entities\AddOn;

class CreateOrUpdateCartRequest extends FormRequest
{

    public function rules()
    {
        $rules = [
            'user_token' => 'required',
        ];
        return $rules;
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        $messages = [
            'user_token.required' => __('apps::frontend.general.user_token_not_found'),
        ];
        return $messages;
    }

    public function withValidator($validator)
    {
        if (auth('api')->check())
            $userToken = auth('api')->user()->id;
        else
            $userToken = $this->user_token ?? null;

        $validator->after(function ($validator) use ($userToken) {

            if (isset($this->addonsOptions) && !empty($this->addonsOptions) && $this->product_type == 'product') {

                foreach ($this->addonsOptions as $k => $value) {

                    if (isset($value['options']) && count($value['options']) > 0) {

                        $addOns = AddOn::where('product_id', $this->product_id)->find($value['id']);
                        if (!$addOns) {
                            return $validator->errors()->add(
                                'addons',
                                __('cart::api.validations.addons.addons_not_found') . ' - ' . __('cart::api.validations.addons.addons_number') . ': ' . $value['id']
                            );
                        }

                        $optionsIds = $addOns->addOnOptions ? $addOns->addOnOptions->pluck('id')->toArray() : [];
                        if ($addOns->type == 'single' && !in_array($value['options'][0], $optionsIds)) {
                            return $validator->errors()->add(
                                'addons',
                                __('cart::api.validations.addons.option_not_found') . ' - ' . __('cart::api.validations.addons.addons_number') . ': ' . $value['options'][0]
                            );
                        }

                        if ($addOns->type == 'multi') {
                            if ($addOns->options_count != null && isset($value['options']) && count($value['options']) > intval($addOns->options_count)) {
                                return $validator->errors()->add(
                                    'addons',
                                    __('cart::api.validations.addons.selected_options_greater_than_options_count') . ': ' . $addOns->translate(locale())->name
                                );
                            }

                            if (isset($value['options']) && count($value['options']) > 0) {
                                foreach ($value['options'] as $i => $item) {
                                    if (!in_array($item, $optionsIds)) {
                                        return $validator->errors()->add(
                                            'addons',
                                            __('cart::api.validations.addons.option_not_found') . ' - ' . __('cart::api.validations.addons.addons_number') . ': ' . $item
                                        );
                                    }
                                }
                            }
                            /*else {
                                return $validator->errors()->add(
                                    'addons', __('cart::api.validations.addons.options.required')
                                );
                            }*/
                        }
                    } else {
                        return $validator->errors()->add(
                            'addons',
                            __('cart::api.validations.addons.options.required')
                        );
                    }
                }
            }
        });
        return true;
    }
}
