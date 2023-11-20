<?php

namespace Database\Seeders;

use App\Models\Bland;
use App\Models\MixPreset;
use Eloquent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MixSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();
        $mixPresets = MixPreset::with('mixes.bland')->where('shop_id', 1)->get();

        foreach ($mixPresets as $mixPreset) {
            $newMixPreset = MixPreset::create([
                'shop_id' => 3,
                'name' => $mixPreset->name
            ]);

            $bland = Bland::with('flavors')->where('shop_id', 3)->first();

        }
    }
}
