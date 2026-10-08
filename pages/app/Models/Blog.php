<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable = [
        'site_id',
        'user_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'fetured_image',
        'status',
        'category_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'meta_head'
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
