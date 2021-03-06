<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActionLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'campaign_id',
        'no_of_records',
        'status',
        'report_status',
        'response',
        'ref_id',
        'flow_action_id',
        'mongo_id',
        'created_at',
        'updated_at',
        'campaign_log_id',
        'action_id',
        'event_received',
        'defaultRecords'
    ];

    protected $casts = [
        'response' => 'json',
        'action_id' => 'json'
    ];

    /**
     * Get all of the campaignReport for the ActionLog
     */
    public function campaignReports()
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

    // get campaign log
    public function campaignLog()
    {
        return $this->belongsTo(CampaignLog::class, 'campaign_log_id');
    }

    /**
     * Get the ref_ids that owns the ActionLog
     */
    public function ref_id()
    {
        return $this->hasMany(ActionLogRefIdRelation::class);
    }
}
