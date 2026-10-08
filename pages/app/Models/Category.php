<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'site_id',
        'status',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
