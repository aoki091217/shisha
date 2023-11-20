<?php

namespace Database\Seeders;

use App\Models\Bland;
use DB;
use Eloquent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BlandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();
        $blands = Bland::where('shop_id', 1)->get();

        foreach ($blands as $bland) {
            Bland::create([
                'shop_id' => 3,
                'name' => $bland->name
            ]);
        }
    }
}
