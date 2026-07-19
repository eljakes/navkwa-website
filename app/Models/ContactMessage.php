<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'name',
        'company',
        'country',
        'email',
        'phone',
        'service',
        'budget',
        'timeline',
        'message',
        'attachment_path',
    ];
}
