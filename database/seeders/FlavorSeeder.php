<?php

namespace Database\Seeders;

use App\Models\Bland;
use App\Models\Flavor;
use Eloquent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FlavorSeeder extends Seeder
{
    public const SHOP_ID = 3;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();
        $oldBlands = Bland::where('shop_id', 1)->get();
        $newBlands = Bland::where('shop_id', self::SHOP_ID)->get();

        foreach ($newBlands as $i => $bland) {
            $flavors = Flavor::where('bland_id', $oldBlands[$i]->bland_id)->where('shop_id', 1)->get();

            foreach ($flavors as $flavor) {
                Flavor::create([
                    'bland_id' => $bland->bland_id,
                    'shop_id' => self::SHOP_ID,
                    'name' => $flavor->name
                ]);
            }

        }
    }
}
