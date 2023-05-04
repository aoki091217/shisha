<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class SituationRequest extends FormRequest
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
        $commonRules = [
            'situation.name' => 'required|max:50',
            'situation.event_type' => 'required',
            'situation.messages.*.message_type' => 'required',
            'situation.messages.*.keyword' => 'nullable|max:50',
            'situation.messages.*.alt_text' => 'nullable|max:400',
            'situation.messages.*.thumbnail_image_url' => 'nullable|mimes:jpg,jpeg,png|dimensions:max_width=1024|max:10000',

            'situation.messages.*.title' => 'nullable|max:40',
            'situation.messages.*.text' => 'required',
            'situation.messages.*.actions.*.label' => 'max:12',
            'situation.messages.*.actions.*.trigger' => 'max:1000'
        ];

        return $commonRules;
    }

    protected function prepareForValidation()
    {
        $situation = $this->situation;
        $messages = [];
        $actions = [];

        foreach ($situation['messages'] as $message) {
            if ($message['message_type'] === 'buttons') {
                $actions = collect($message['actions'])->filter(function ($action) {
                    return isset($action['label']) && isset($action['trigger']);
                })->toArray();

                $messages[] = array_merge($message, ['actions' => $actions]);
            } else {
                $messages[] = $message;
            }
        }

        $situation = array_merge($situation, ['messages' => $messages]);
        $this->merge(['situation' => $situation]);
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
