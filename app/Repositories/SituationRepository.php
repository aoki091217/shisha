<?php

namespace App\Repositories;

use App\Models\Situation;
use App\Services\SituationService;

class SituationRepository
{
    public function get()
    {
        if (auth()->user()->role_id === 1) {
            return Situation::get();
        } else {
            return Situation::where('shop_id', auth()->user()->member->shop_id)->get();
        }
    }

    public function getByFollowEvent()
    {
        $query = Situation::query()->where(Situation::EVENT_TYPE, 1);

        if (auth()->user()->role_id === 1) {
            return $query->get();
        } else {
            return $query->where('shop_id', auth()->user()->member->shop_id)->get();
        }
    }
}

?>
