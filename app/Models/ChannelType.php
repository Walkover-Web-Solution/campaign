<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChannelType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'configurations',
    ];

    protected $casts = [
        'configurations' => 'json',
    ];

    protected $hidden = array(
        'created_at',
        'updated_at'
    );

    // This function executed when channel type model initiated
    protected static function booted()
    {
        static::addGlobalScope('only_email_sms', function (Builder $builder) {
            $emailType = 1;
            $sms = 2;
            $whatsapp = 3;
            $voice = 4;
            $builder->whereIn('id', [$emailType, $sms]);
        });
    }

    /**
     * Get all of the post's flowActions.
     */
    public function flowActions()
    {
        return $this->morphMany(FlowAction::class, 'linked');
    }

    public function conditions()
    {
        return $this->belongsToMany(Condition::class)->using(ChannelTypeCondition::class);
    }
}
