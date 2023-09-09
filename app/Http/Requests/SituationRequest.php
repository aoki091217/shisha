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
            'situation.shop_id' => 'required',
            'situation.name' => 'required|max:50',
            'situation.event_type' => 'required',
            'situation.messages.*.message_type' => 'required',
            'situation.messages.*.keyword' => 'nullable|max:50',
            'situation.messages.*.alt_text' => 'nullable|max:400',
        ];

        return $commonRules;
    }

    protected function prepareForValidation()
    {
        $carousels = [];
        $messages = [];

        $requestMessages = collect($this->situation['messages'])->reject(function ($item) {
            if ($this->method() === 'PATCH') {
                return !isset($item['id']) || $item['disabled'] == 1;
            }
        });

        foreach ($requestMessages as $messageIndex => $message) {
            if ($message['message_type'] === 'carousel') {
                $carousels = collect($message['carousels'])->reject(function ($item) {
                    return is_null($item['text']);
                })->toArray();

                $titleNullCount = 0;
                $titleFillCount = 0;
                $imageNullCount = 0;
                $imageFillCount = 0;
                foreach ($carousels as $carousel) {
                    if (is_null($carousel['title'])) {
                        $titleNullCount++;
                    } else {
                        $titleFillCount++;
                    }

                    if (isset($carousel['thumbnail_image_url'])) {
                        $imageFillCount++;
                    } else {
                        $imageNullCount++;
                    }
                }

                if (count($carousels) === $titleNullCount || count($carousels) === $titleFillCount) {
                    $messages[] = array_merge($message, ['carousels' => $carousels]);
                } elseif (count($carousels) !== $imageNullCount || count($carousels) !== $imageFillCount) {
                    $messages[] = array_merge($message, ['carousels' => $carousels]);
                } else {
                    $this->merge(['validated', true]);

                    throw new HttpResponseException(
                        redirect($this->getRedirectUrl())
                        ->withInput($this->input())
                        ->with("situation.messages.{$messageIndex}.diff", 'メッセージ内のカルーセルのタイトルまたは画像を一つでも設定する場合は、すべてのカルーセルに設定してください。')
                    );
                }
            } else {
                $messages[] = $message;
            }
        }

        $situation = array_merge($this->situation, ['messages' => $messages]);
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
