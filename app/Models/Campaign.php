<?php

namespace App\Models;

use App\Http\Resources\CustomResource;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'token_id',
        'is_active',
        'configurations',
        'meta',
        'slug',
        'style',
        'module_data'
    ];

    protected $casts = array(
        'meta' => 'object',
        'configurations' => 'object',
        'style' => 'json',
        'module_data' => 'json'
    );


    protected $hidden = array(
        'created_at',
        'updated_at',
        'user_id',
        "company_id",
        "token_id",
        'deleted_at',
        'meta'
    );

    public static function boot()
    {
        parent::boot();

        static::creating(function ($campaign) {
            /**
             * generating  the slug name
             * logic : creating the $slug string as "campaignName-campaignnumber"
             * if slug is empty in campaign table then ad  the $slug value in the refrenced campaign_id
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
            if (empty($campaign->style)) {
                $campaign->style = array(
                    "x" => 0,
                    "y" => 0,
                    "width" => 150,
                    "height" => 100
                );
            }

            if (empty($campaign->module_data)) {
                $campaign->module_data = array(
                    "op_start" => null,
                    "op_start_type" => null
                );
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
        return $this->belongsTo(Token::class, 'token_id');
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
    public function flowActions()
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

    public function resolveRouteBinding($value, $field = null)
    {
        try {
            $res = JWTDecode(request()->header('authorization'));
            $company = Company::where('ref_id', $res->company->id)->first();
            if (empty($company))
                throw new Exception();
        } catch (\Exception $e) {
            return new CustomResource(["message" => "Unauthorized"], true);
        }
        return Campaign::where('company_id', $company->id)->where('slug', $value)->first();
    }
}
