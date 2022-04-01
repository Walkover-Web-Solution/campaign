<?php

namespace App\Models;

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
}
