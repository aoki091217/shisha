<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

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
        return [
            'bill.shop_id' => 'required',
            'bill.member_id' => 'required',
            'bill.customers.*' => 'required',
            'bill.top_change' => 'numeric',
            'bill.amount' => 'required',
            'bill.date' => 'required',
            'bill.time' => 'required',
        ];
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
        $bill = array_merge($this->bill, [
            'amount' => mb_convert_kana($this->bill['amount'], 'n'),
            'bill_date' => Carbon::parse($this->bill['date'])->setTimeFromTimeString($this->bill['time'])->toDateTimeString()
        ]);

        $this->merge([
            'bill' => $bill
        ]);
    }
}
