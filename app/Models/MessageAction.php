<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'type',
        'label',
        'action'
    ];

    public function getActionTypeAttribute()
    {
        return $this->type == 1 ? 'message' : 'uri';
    }
}
