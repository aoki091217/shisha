<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Storage;

class Message extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'situation_id',
        'type',
        'keyword',
        'alt_text',
        'title',
        'text',
        'turn',
        'send_type'
    ];

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($article) {
            $dir = "template/{$article->situation_id}/{$article->id}";
            Storage::disk('public')->deleteDirectory($dir);

            $article->carousels()->delete();
        });
    }

    public function getMessageTypeAttribute()
    {
        return $this->type == 2 ? 'carousel' : 'text';
    }

    public function getMessageSendTypeAttribute()
    {
        return $this->send_type == 2 ? 'reply' : 'push';
    }

    public function situation()
    {
        return $this->belongsTo(Situation::class, 'situation_id');
    }

    public function carousels()
    {
        return $this->hasMany(Carousel::class);
    }
}
