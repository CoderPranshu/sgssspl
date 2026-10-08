<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $fillable = [
        'user_id',
        'domain',
        'slug',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
