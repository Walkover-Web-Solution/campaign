<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActionLogRefIdRelation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'action_log_id',
        'ref_id',
        'response',
        'status',
        'no_of_records'
    ];

    protected $casts = [
        'response' => 'json'
    ];

    /**
     * Get the ref_ids that owns the ActionLog
     */
    public function actionLogs()
    {
        return $this->belongsTo(ActionLog::class, 'action_log_id');
    }
}
