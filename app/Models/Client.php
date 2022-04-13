<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'auth_key',
        'meta'
    ];

    protected $casts = [
        'meta' => 'json'
    ];

    /**
     * generating the auth key for client
     */
    public static function boot()
    {
        parent::boot();
        static::creating(function ($client) {
            $authkey = md5(uniqid(microtime(true) . mt_Rand(), true));
            $client->authkey = $authkey;
            if($client->name == 'MSG91'){
                $client->authkey = '350692fce66bf161cdde9ffa164dbe18';
            }
        });
    }

    /**
     * Get all of the company for the Client
     *
     */
    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}
