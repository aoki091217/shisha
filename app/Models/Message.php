<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'situation_id',
        'type',
        'keyword',
        'alt_text',
        'title',
        'text',
        'thumbnail_image_url',
        'turn',
        'send_type'
    ];

    public function getMessageTypeAttribute()
    {
        return $this->type == 2 ? 'buttons' : 'text';
    }

    public function getMessageSendTypeAttribute()
    {
        return $this->send_type == 2 ? 'reply' : 'push';
    }

    public function messageActions()
    {
        return $this->hasMany(MessageAction::class);
    }
}
