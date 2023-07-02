<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Storage;

class Carousel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'message_id',
        'thumbnail_image_url',
        'title',
        'text'
    ];

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($article) {
            if (!is_null($article->thumbnail_image_url)) {
                Storage::disk('public')->deleteDirectory($article->thumbnail_image_url);
            }

            $article->carouselActions()->delete();
        });
    }

    public function carouselActions()
    {
        return $this->hasMany(CarouselAction::class);
    }
}
