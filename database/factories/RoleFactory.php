<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        static $index = 0;
        $name = [
            'システム管理者',
            '店舗管理者',
            '従業員'
        ];

        return [
            'name' => $name[$index++]
        ];
    }
}
