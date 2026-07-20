<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    protected $fillable = [
        'name',
        'email',
        'source_page',
        'status',
        'email_verified_at',
        'unsubscribed_at',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
        ];
    }
}
