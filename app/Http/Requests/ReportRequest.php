<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
{
    const START_DATE = 'start_date';
    const END_DATE = 'end_date';
    const HASH = 'hash';

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
            //
        ];
    }

    public function getStartDate(): Carbon
    {
        return Carbon::parse($this->{self::START_DATE});
    }

    public function getEndDate(): Carbon
    {
        return Carbon::parse($this->{self::END_DATE});
    }

    public function getHash(): ?string
    {
        return $this->{self::HASH};
    }

}
