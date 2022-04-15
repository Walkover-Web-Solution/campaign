<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'created_at',
        'updated_at',
        'mongo_uid',
        'no_of_contacts',
        'status'
    ];

    protected $hidden = [
        'created_at',
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

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }
}
