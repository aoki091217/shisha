<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Storage;

class Situation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'event_type'
    ];

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($article) {
            $dir = "template/{$article->id}";
            Storage::disk('public')->deleteDirectory($dir);

            $article->messages()->delete();
        });
    }

    public function getReceiveEventAttribute()
    {
        switch ($this->event_type) {
            case 1:
                return '友達追加';
            case 2:
                return 'テキストの受信';
            case 3:
                return 'アンケート';
            case 4:
                return 'ブロック';
        }
    }

    public function getCreatedDatetimeAttribute()
    {
        return Carbon::parse($this->created_at)->format('Y年m月d日 H時i分');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
