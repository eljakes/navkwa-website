<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'content_type',
        'title',
        'slug',
        'description',
        'body',
        'image_path',
        'seo_title',
        'seo_description',
        'status',
        'display_order',
        'publish_at',
    ];

    protected function casts(): array
    {
        return [
            'display_order' => 'integer',
            'publish_at' => 'datetime',
        ];
    }
}
