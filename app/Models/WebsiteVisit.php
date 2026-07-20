<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteVisit extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'path',
        'route_name',
        'method',
        'session_id',
        'ip_address',
        'country',
        'user_agent',
        'referer',
        'visited_at',
    ];

    protected function casts(): array
    {
        return [
            'visited_at' => 'datetime',
        ];
    }
}
