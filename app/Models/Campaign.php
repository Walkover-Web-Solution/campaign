<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'campaign_type_id',
        'company_token_id',
        'is_active',
        'configurations',
        'meta',
        'slug'
    ];

    protected $casts = array(
        'meta' => 'object',
        'configurations' => 'object',
    );


    protected $hidden = array(
        'created_at',
        'updated_at',
        'user_id',
        "company_id",
        "company_token_id",
        'deleted_at',
        'meta'
    );

    public static function boot()
    {
        parent::boot();

        static::creatxing(function ($campaign) {
            /**
             * generating  the slug name
            */
            $i = 0;
            while (true) {
                $slug = \Str::slug($campaign->name, '-');
                if ($i) {
                    $slug = $slug . $i;
                }

                if (!Campaign::where('slug', $slug)->where('company_id', $campaign->company_id)->exists()) {
                    $campaign->slug = $slug;
                    break;
                }
                $i++;
            }
        });
    }


    /**
     * Get the user that owns the Campaign
    */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the company that owns the Campaign
     *
    */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Get the token that owns the Campaign
     *
    */
    public function token()
    {
        return $this->belongsTo(token::class, 'company_token_id');
    }

    /**
     * Get all of the actionLogs for the Campaign
    */
    public function actionLogs()
    {
        return $this->hasMany(ActionLog::class, 'campaign_id');
    }

    /**
     * Get all of the flowAction for the Campaign
    */
    public function flowAction()
    {
        return $this->hasMany(FlowAction::class, 'campaign_id');
    }

    /**
     * Get all of the campaignReports for the Campaign
    */
    public function campaignReports()
    {
        return $this->hasMany(CampaignReport::class, 'campaign_id');
    }

}
