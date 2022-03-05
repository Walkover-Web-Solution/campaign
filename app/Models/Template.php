<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    protected $fillable = [
        'flow_action_id',
        'template_id',
        'variables'
    ];

    protected $casts = [
        'variables'=>'json',
    ];

    protected $hidden = array(
        'created_at',
        'updated_at'
    );

    /**
     * Get all of the templateDetail for the Template
    */
    public function templateDetail()
    {
        return $this->hasMany(TemplateDetail::class, 'template_id');
    }

    /**
     * Get all of the flowAction for the Template
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function flowAction()
    {
        return $this->hasMany(FlowAction::class, 'flow_action_id');
    }

}
