<?php

namespace App\Http\Requests;

use App\Models\Code;
use Crypt;
use Hash;
use Illuminate\Foundation\Http\FormRequest;
use Str;

class CodeRequest extends FormRequest
{
    public const DOT = 'codes.';

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
            self::DOT . Code::SHOP_ID => 'required',
            self::DOT . Code::NAME => 'required',
            self::DOT . Code::SITUATION_ID => 'required',
            self::DOT . Code::KIND => 'required',
            self::DOT . Code::PARAMETER => 'required',
            self::DOT . Code::SCRIPT => 'required',
            self::DOT . Code::NOTES => 'nullable',
        ];
    }

    public function getCodes(): array
    {
        return $this->codes;
    }

    protected function prepareForValidation()
    {
        $codes = $this->getCodes();
        $notes = is_null($codes['notes']) ? '' : $codes['notes'];
        $this->merge(['codes' => array_merge($codes, ['notes' => $notes])]);
    }

    protected function passedValidation()
    {
        $codes = $this->getCodes();

        $hash = Str::random(9);

        $this->merge([
            'codes' => array_merge($codes, ['hash' => $hash])
        ]);
    }
}
