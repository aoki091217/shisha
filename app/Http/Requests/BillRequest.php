<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Route;

class BillRequest extends FormRequest
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
        if (Route::is('bill.draft')) {
            return [
                'bill.member_id' => 'required',
                'bill.customers' => 'nullable',
                'bill.mixes.*' => 'nullable',
                'bill.top_change' => 'numeric',
                'bill.amount' => 'nullable',
                'bill.date' => 'nullable',
                'bill.time' => 'nullable',
            ];
        } else {
            return [
                'bill.member_id' => 'required',
                'bill.customers' => 'required',
                'bill.mixes.*' => 'required',
                'bill.top_change' => 'numeric',
                'bill.amount' => 'required',
                'bill.date' => 'required',
                'bill.time' => 'required',
            ];
        }

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

    protected function passedValidation()
    {
        $mixes = collect($this->bill['mixes'])->reject(function ($mix) {
            return is_null($mix['mix_id']);
        })->toArray();

        if (empty($mixes) && !Route::is('bill.draft')) {
            throw new HttpResponseException(redirect($this->getRedirectUrl())->withInput($this->input()));
        }

        $is_draft = Route::is('bill.draft') ? 1 : 0;

        $bill = array_merge($this->bill, [
            'amount' => mb_convert_kana($this->bill['amount'], 'n'),
            'bill_date' => Carbon::parse($this->bill['date'])->setTimeFromTimeString($this->bill['time'])->toDateTimeString(),
            'mixes' => $mixes,
            'is_draft' => $is_draft
        ]);

        $this->merge([
            'bill' => $bill
        ]);
    }
}
