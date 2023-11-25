<?php

namespace Database\Seeders;

use App\Models\Bland;
use App\Models\Flavor;
use App\Models\Mix;
use App\Models\MixPreset;
use DB;
use Eloquent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MixSeeder extends Seeder
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

        DB::transaction(function () {
            $mixPresets = MixPreset::with(['mixes.bland', 'mixes.flavor'])->where('shop_id', 1)->get();

            foreach ($mixPresets as $mixPreset) {
                $newMixPreset = MixPreset::create([
                    'shop_id' => self::SHOP_ID,
                    'name' => $mixPreset->name
                ]);

                foreach ($mixPreset->mixes as $mix) {
                    $bland = Bland::find($mix->bland_id);
                    $newBland = Bland::with('flavors')->where('name', $bland->name)->where('shop_id', self::SHOP_ID)->first();

                    $flavor = Flavor::find($mix->flavor_id);
                    $newFlavor = $newBland->flavors->where('name', $flavor->name)->first();

                    Mix::create([
                        'preset_id' => $newMixPreset->id,
                        'bland_id' => $newBland->bland_id,
                        'flavor_id' => $newFlavor->flavor_id
                    ]);
                }
            }
        });
    }
}
