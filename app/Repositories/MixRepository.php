<?php

namespace App\Repositories;

use App\Models\Mix;
use App\Models\MixPreset;
use DB;

class MixRepository
{
    public function get()
    {
        if (auth()->user()->role_id === 1) {
            return MixPreset::get();
        } else {
            return MixPreset::where('shop_id', auth()->user()->member->shop_id)->get();
        }
    }

    public function relate()
    {
        return MixPreset::with(['mixes.bland', 'mixes.flavor']);
    }

    public function paginate()
    {
        return MixPreset::paginate(10);
    }

    public function search($words)
    {
        return MixPreset::search($words);
    }

    public function find($id)
    {
        return MixPreset::find($id);
    }

    public function findMix($id)
    {
        return Mix::find($id);
    }

    public function store($request)
    {
        DB::transaction(function () use ($request) {
            if (auth()->user()->role_id !== 1) {
                $request = array_merge($request, ['shop_id' => auth()->user()->member->shop_id]);
            }

            $mixPresets = new MixPreset();
            $mixPresets->fill($request)->save();

            foreach ($request['presets']['regist'] as $preset) {
                $mix = new Mix();
                $mix->fill(array_merge($preset, ['preset_id' => $mixPresets->id]))->save();
            }
        });
    }

    public function update($request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            $mixPresets = $this->find($id);
            $mixPresets->fill($request)->save();

            foreach ($request['presets']['regist'] as $preset) {
                if (!is_null($preset['mix_id'])) {
                    $mix = $this->findMix($preset['mix_id']);
                } else {
                    $mix = new Mix();
                }

                $mix->fill(array_merge($preset, ['preset_id' => $mixPresets->id]))->save();
            }

            foreach ($request['presets']['delete'] as $preset) {
                if (!is_null($preset['mix_id'])) {
                    $mix = $this->findMix($preset['mix_id'])->delete();
                }
            }
        });
    }

    public function delete($id)
    {
        DB::transaction(function () use ($id) {
            $flavor = $this->find($id);
            $flavor->delete();
        });
    }
}

?>
