<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Template extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'flow_action_id',
        'template_id',
        'variables'
    ];

    protected $casts = [
        'variables' => 'json',
    ];

    protected $hidden = array(
        'id',
        'flow_action_id',
        'created_at',
        'updated_at'
    );

    /**
     * Get all of the templateDetail for the Template
     */
    public function templateDetails()
    {
        return $this->belongsTo(TemplateDetail::class, 'template_id');
    }

    /**
     * Get all of the flowAction for the Template
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function flowActions()
    {
        return $this->hasOne(FlowAction::class, 'flow_action_id');
    }
}
