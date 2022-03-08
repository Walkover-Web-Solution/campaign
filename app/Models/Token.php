<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    use HasFactory;

    protected $fillable=[
    	'name',
        'is_active',
        'throttle_limit',
        'temporary_throttle_limit',
    ];

    protected $casts=array(
      'is_primary'=>'boolean',
      'is_active'=>'boolean'
    );


    protected $hidden=array(
        'created_at',
        'updated_at',
        'user_id',
        'company_id',
        'deleted_at'
    );


   public static function boot(){
       parent::boot();

       static::creating(function($companyToken){
            $company=Company::find($companyToken->company_id);
            $token=md5(uniqid(microtime(true).mt_Rand(), true));
            $companyToken->token=$token;
            $companyToken->is_active=true;
            $companyToken->is_primary=$company->tokens->isEmpty()?true:false;
       });
   }

   public function properties(){
      return $this->hasOne(CompanyTokenProperty::class)->withDefault([
        'throttle_limit'=>'1:30',
        'whitelist_ips'=>[],
        'blacklist_ips'=>[]
      ]);
   }
    /**
     * Get all of the ips for the Token
    */
    public function ips()
    {
        return $this->hasMany(CompanyTokenIp::class);
    }

    /**
     * Get the company that owns the Token
    */
    public function company()
    {
        return $this->belongsTo(Company::class,'company_id');
    }

    /**
     * Get all of the campaigns for the Token
    */
    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }
}
