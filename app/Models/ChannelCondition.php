<?php

namespace App\Models;

use Illuminate\Broadcasting\Channel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChannelCondition extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel_id ',
        'condition_id',
    ];

    protected $hidden = array(
        'created_at',
        'updated_at',
    );

    public function channel()
    {
        return $this->belongsTo(Channel::class, 'channel_id');
    }

    public function condition()
    {
        return $this->belongsTo(Condition::class, 'condition_id');
    }
}
