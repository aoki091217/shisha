<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class LineRequest extends FormRequest
{
    private const SHOP_ID = 'shop_id';
    private const LIFF_STATE = 'liff_state';

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
            'shop_id' => 'nullable|integer'
        ];
    }

    public function getShopId(): int
    {
        return (int) $this->{self::SHOP_ID};
    }

    public function getLiffState(): string
    {
        return $this->{self::LIFF_STATE} ?? '';
    }
}
