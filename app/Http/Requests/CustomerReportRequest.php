<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class CustomerReportRequest extends FormRequest
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
            'startDate' => 'nullable|date',
            'endDate' => 'nullable|date|after_or_equal:startDate',
            'shopIds' => 'array',
            'shopIds.*' => 'nullable|integer',
        ];
    }

    public function startDate(): ?Carbon
    {
        return $this->input('startDate') ? Carbon::parse($this->input('startDate')) : null;
    }

    public function endDate(): ?Carbon
    {
        return $this->input('endDate') ? Carbon::parse($this->input('endDate')) : null;
    }

    public function shopIds(): array
    {
        return array_filter(array_map(fn($v) => filter_var($v, FILTER_VALIDATE_INT), $this->input('shopIds', [])));
    }

}
