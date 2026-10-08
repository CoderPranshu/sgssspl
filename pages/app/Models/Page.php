<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'body',
        'content_mode',
        'custom_html',
        'custom_css',
        'custom_js',
        'swiper_enabled',
        'head_option',
        'breadcrum_image',
        'image',
        'status',
        'header_option'
    ];

    public function assets()
    {
        return $this->hasMany(PageAsset::class);
    }

    public function faqs()
    {
        return $this->hasMany(PageFaq::class);
    }
}
