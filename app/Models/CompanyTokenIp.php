<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyTokenIp extends Model
{
    use HasFactory;

    protected $table = 'company_token_ips';
    protected $fillable = [
        'ip',
        'token_id',
        'expires_at',
        'ip_type_id'
    ];
    protected $hidden = array(
        'created_at',
        'updated_at'
    );

    public static function boot()
    {
        parent::boot();

        static::saving(function ($ip) {
            $ip->expires_at = date('Y-m-d H:i:s', $ip->token->temporary_throttle_time + time());
        });
    }

    public function getExpiresAtAttribute($value)
    {
        if ($this->ip_type_id != 3) {
            return '-';
        }
        return GMTToIST($value); // function comes from app\helper.php
    }


    /**
     * Get the iptype that owns the CompanyTokenIp
     */
    public function type()
    {
        return $this->belongsTo(IpType::class, 'ip_type_id');
    }

    /**
     * Get the token that owns the CompanyTokenIp
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function token()
    {
        return $this->belongsTo(Token::class, 'token_id');
    }
}
