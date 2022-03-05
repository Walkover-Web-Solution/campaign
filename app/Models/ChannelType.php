<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChannelType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'configuration',
    ];

    protected $casts = [
        'configuration' => 'json',
    ];

    protected $hidden = array(
        'created_at',
        'updated_at'
    );

    /**
     * Get all of the flowAction for the ChannelType
    */
    public function flowAction()
    {
        return $this->hasMany(FlowAction::class);
    }
}
