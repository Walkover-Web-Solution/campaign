<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateDetail extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'template_id',
        'content',
        'meta'
    ];

    protected $hidden = array(
        'created_at',
        'updated_at'
    );


    /**
     * Get the template that owns the TemplateDetail
    */
    public function template()
    {
        return $this->hasMany(Template::class, 'template_id');
    }

    
}

