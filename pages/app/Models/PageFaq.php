<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageFaq extends Model
{
    protected $fillable = [
        'faq_question',
        'faq_answer',
        'page_id'
    ];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
