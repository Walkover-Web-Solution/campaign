<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Condition extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'is_boolean',
        'wait_to_fail',
    ];

    protected $casts = array(
        'is_boolean' => 'boolean',
        'wait_to_fail' => 'boolean'
    );

    protected $hidden = array(
        'created_at',
        'updated_at',
    );

    /**
     * Get all of the post's flowActions.
     */
    public function flowActions()
    {
        return $this->morphMany(FlowAction::class, 'linked');
    }

    public function channelConditions()
    {
        return $this->hasMany(ChannelCondition::class);
    }
}
