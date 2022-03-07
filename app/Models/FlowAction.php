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
        'table_associated',
        'is_condition'
    ];
    protected $casts = array(
        'is_condition' => 'boolean',
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
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function template()
    {
        return $this->belongsTo(Template::class, 'flow_action_id');
    }

    /**
     * Get the channelType that owns the FlowAction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function channelType()
    {
        return $this->belongsTo(ChannelType::class, 'linked_id');
    }

    /**
     * Get the condition that owns the FlowAction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function condition()
    {
        return $this->belongsTo(Condition::class, 'linked_id');
    }

    /**
     * Get the campaign that owns the FlowAction
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }
}
