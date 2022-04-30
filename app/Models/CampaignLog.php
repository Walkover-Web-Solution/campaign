<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'mongo_uid',
        'no_of_contacts',
        'status',
        'ip',
        'need_validation'
    ];

    protected $casts = [
        'need_validation' => 'boolean'
    ];

    protected $hidden = [
        'mongo_uid',
        'updated_at'
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
