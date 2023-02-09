<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class FlavorRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'flavor.bland_id' => 'required',
        ];

        if ($this->method() === 'PATCH') {
            $rules = array_merge($rules, [
                'flavor.name' => 'required|max:50'
            ]);
        } else {
            $rules = array_merge($rules, [
                'flavor.names.*' => Rule::forEach(function ($value, $attribute) {
                    if (explode('.', $attribute)[2] == 0) {
                        $rule = 'required|max:50';
                    } else {
                        $rule = 'nullable|max:50';
                    }
                    return $rule;
                })
            ]);
        }

        return $rules;
    }

    protected function failedValidation(Validator $validator)
    {
        $this->merge(['validated', true]);
        throw new HttpResponseException(
            redirect($this->getRedirectUrl())
            ->withErrors($validator)
            ->withInput($this->input())
        );
    }
}
