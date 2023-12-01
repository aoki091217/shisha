<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class LineRequest extends FormRequest
{
    private const SHOP_ID = 'sid';
    private const LINE_TOKEN = 'line_token';
    private const UTM_RESOURCE = 'utm_resource';
    private const QUERY_PARAMS = 'query_params';

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
        ];
    }

    public function getShopId(): int
    {
        return (int) $this->{self::SHOP_ID};
    }

    public function getLineToken(): string
    {
        return $this->{self::LINE_TOKEN} ?? '';
    }

    public function getUtmResouce(): string
    {
        return $this->{self::UTM_RESOURCE} ?? '';
    }

    public function getQueryParams(): array
    {
        return $this->{self::QUERY_PARAMS} ?? [];
    }
}
