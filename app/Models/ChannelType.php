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
            $email = 1;
            $sms = 2;
            $whatsapp = 3;
            $voice = 4;
            $rcs = 5;
            $condition = 6;
            $builder->whereIn('id', [$email, $sms, $rcs, $condition, $whatsapp]);
        });
    }

    /**
     * Get all of the post's flowActions.
     */
    public function flowActions()
    {
        return $this->morphMany(FlowAction::class, 'linked');
    }

    /**
     * Get all events of this FLow Action
     */
    public function events()
    {
        return $this->belongsToMany(Event::class)->using(ChannelTypeEvent::class);
    }

    /**
     * Purpose of creating events function as conditions is to show on UI as till UI changes conditions key to events
     */
    public function conditions()
    {
        return $this->belongsToMany(Event::class)->using(ChannelTypeEvent::class);
    }
}
