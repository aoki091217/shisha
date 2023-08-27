<?php

namespace App\Services;

class RoleService
{
    private const HIGH = [1];
    private const MID = [1, 2];
    private const LOW = [1, 2, 3];

    public function findRolePreset($role)
    {
        switch ($role) {
            case 'high':
                return self::HIGH;
            case 'mid':
                return self::MID;
            case 'low':
                return self::LOW;
        }
    }
}

?>
