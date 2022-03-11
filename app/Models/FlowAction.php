<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlowAction extends Model
{
    use HasFactory;

    protected $fillables = [
        'campaign_id',
        'linked_id',
        'linked_type',
        'is_condition',
        'configurations'
    ];
    protected $casts = array(
        'is_condition' => 'boolean',
        'configurations'=>'object'
    );
    protected $hidden = array(
        'created_at',
        'updated_at',
        'parent_id'
    );

    /**
     * Get the actionLog that owns the FlowAction
     */
    public function actionLog()
    {
        return $this->belongsTo(ActionLog::class, 'campaign_id');
    }

    /**
     * Get the template that owns the FlowAction
     */
    public function template()
    {
        return $this->belongsTo(Template::class, 'flow_action_id');
    }

    /**
     * Get the campaign that owns the FlowAction
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    /**
     * Get all of the owning linked models.
     */
    public function linked()
    {
        return $this->morphTo();
    }
}
