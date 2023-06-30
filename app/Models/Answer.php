<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Answer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'carousel_id',
        'carousel_action_id',
        'customer_id'
    ];

    public function carousel()
    {
        return $this->belongsTo(Carousel::class, 'carousel_id');
    }

    public function carouselAction()
    {
        return $this->belongsTo(CarouselAction::class, 'carousel_action_id');
    }
}
