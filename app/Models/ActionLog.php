<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActionLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'campaign_id',
        'no_of_records',
        'status',
        'reason',
        'ip',
        'ref_id',
        'flow_action_id'
    ];

    protected $hidden = array(
        'created_at',
        'updated_at',
    );

    /**
     * Get all of the campaignReport for the ActionLog
     */
    public function campaignReport()
    {
        return $this->hasMany(CampaignReport::class);
    }

    /**
     * Get the flowAction that owns the ActionLog
   */
    public function flowAction()
    {
        return $this->belongsTo(FlowAction::class, 'flow_action_id');
    }

    /**
     * Get the campaign that owns the ActionLog
   */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

}
