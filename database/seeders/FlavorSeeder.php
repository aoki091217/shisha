<?php

namespace Database\Seeders;

use App\Models\Bland;
use App\Models\Flavor;
use Eloquent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FlavorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();
        $blands = Bland::with('flavors')->where('shop_id', 1)->get();

        foreach ($blands as $bland) {
            foreach ($bland->flavors as $flavor) {
                Flavor::create([
                    'bland_id' => $bland->bland_id,
                    'shop_id' => 3,
                    'name' => $flavor->name
                ]);
            }

        }
    }
}
