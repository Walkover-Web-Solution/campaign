<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable=[
        'name',
        'email',
        'auth_key',
        'meta'
    ];

    protected $casts=[
        'meta'=>'json'
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($company ){
            //will generate random auth_key for client
        });
    }

    /**
     * Get all of the companies for the Client
     *
     */
    public function companies()
    {
        return $this->hasMany(Company::class);
    }

}
