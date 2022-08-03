<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use phpDocumentor\Reflection\Types\Boolean;

class CampaignLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'campaign_id',
        'mongo_uid',
        'no_of_contacts',
        'status',
        'ip',
        'need_validation',
        'is_paused',
        'can_retry',
        'loop_detected'
    ];

    protected $casts = [
        'need_validation' => 'boolean',
        'is_paused' => 'boolean',
        'can_retry' => 'boolean',
        'mongo_deleted' => 'boolean',
        'loop_detected' => 'boolean'
    ];

    protected $hidden = [
        'mongo_uid',
        'updated_at',
        'mongo_deleted'
    ];


    public static function boot()
    {
        parent::boot();

        static::creating(function ($campaignLog) {
            if (empty($campaignLog->status)) {
                $campaignLog->status = 'Running';
            }
        });
    }


    // get campaigns
    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    //get all actionLogs
    public function actionLogs()
    {
        return $this->hasMany(ActionLog::class);
    }
}
