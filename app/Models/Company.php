<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    protected $fillable = [
        ' client_id',
        'name',
        'ref_id',
        'email',
        'authkey'
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($company ){
            //will generate random auth_key for client
        });
    }

    /**
     * The users that belong to the Company
     */
    public function users(){
        return  $this->belongsToMany(User::class,'company_user')->using(CompanyUser::class)
        ->withPivot('ref_id')
        ->withTimestamps();
     }

     /**
      * Get all of the tokens and campaign for the Company
    */
      public function tokens(){
        return $this->hasMany(CompanyToken::class);
       }


       public function campaigns(){
        return $this->hasMany(Campaign::class);
       }
}
