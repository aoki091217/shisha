<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
{
    const START_YEAR = 'start_year';
    const END_YEAR = 'end_year';
    const HASH = 'hash';
    const QUERY = 'query';

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

    public function getStartYear(): Carbon
    {
        return Carbon::parse($this->{self::START_YEAR});
    }

    public function getEndYear(): Carbon
    {
        return Carbon::parse($this->{self::END_YEAR});
    }

    public function getHash(): ?string
    {
        return $this->{self::HASH};
    }

    public function getQuery(): ?string
    {
        return $this->{self::QUERY};
    }
}
