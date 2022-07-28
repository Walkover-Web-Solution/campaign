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
        'canRetry',
        'mongo_deleted'
    ];

    protected $casts = [
        'need_validation' => 'boolean',
        'is_paused' => 'boolean',
        'canRetry' => 'boolean',
        'mongo_deleted' => 'boolean'

    ];

    protected $hidden = [
        'mongo_uid',
        'updated_at',
        'canRetry'
    ];
    protected $appends = array('can_retry');

    public static function boot()
    {
        parent::boot();

        static::creating(function ($campaignLog) {
            if (empty($campaignLog->status)) {
                $campaignLog->status = 'Running';
            }
        });
    }

    /**
     * Get the campaignLog's canRetry.
     *
     * @param  string  $value
     * @return string
     */
    public function getCanRetryAttribute($value)
    {
        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        $this->attributes['canRetry'] = $value;
        return $this->attributes['canRetry'];
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
