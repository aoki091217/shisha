<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'line/webhook',
        // POSTされたLINEのaccessTokenで認証を行うためCSRF対策は不要
        'line/liff/*/verify',
    ];
}
