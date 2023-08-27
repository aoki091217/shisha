<?php

namespace App\Services;

use App\Models\Role;
use Auth;

class AuthService
{
    public function __construct(
        private $user
    ) {
        $this->user = Auth::user();
    }

    public function isHigh()
    {
        return $this->user->role_id === 1;
    }

    public function isMid()
    {
        return in_array($this->user->role_id, [1, 2]);
    }
}

?>
