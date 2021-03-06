<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IpType extends Model
{
    use HasFactory;

    protected $table='ip_types';

    protected $fillable=['name'];

     protected $hidden=array(
        'created_at',
        'updated_at'
    );
}
