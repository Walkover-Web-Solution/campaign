<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Condition extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'configurations'
    ];

    protected $casts = array(
        'configurations' => 'json',
    );


    protected $hidden = array(
        'created_at',
        'updated_at',
    );

    /**
     * get all filters of this Condition
     */
    public function filters()
    {
        return $this->belongsToMany(Filter::class);
    }
    /**
     * will remove when UI gets updated - TASK
     */
    public function conditions()
    {
        return $this->belongsToMany(Filter::class);
    }
}
