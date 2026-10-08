<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageAsset extends Model
{
    protected $fillable = [
        'page_id', 'asset_type', 'section_name', 'image_label', 'reference',
        'expected_filename', 'alt_text', 'stored_filename', 'stored_path', 'status',
    ];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
