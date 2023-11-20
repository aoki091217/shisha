<?php

namespace App\Services;

use App\Http\Requests\Api\LineRequest;

class LiffService
{
    public function save(LineRequest $request): void
    {
        $queryParam = '';
        foreach ($request->query_param as $key => $value) {
            \Log::debug($key);
            \Log::debug($value);
            $queryParam = $queryParam . "&{$key}={$value}";
        }
        \Log::debug($queryParam);
    }
}

?>
