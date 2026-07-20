<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultationBooking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_name',
        'company',
        'email',
        'phone',
        'service',
        'meeting_at',
        'meeting_type',
        'meeting_link',
        'assigned_consultant',
        'status',
        'client_notes',
        'internal_notes',
    ];

    protected function casts(): array
    {
        return [
            'meeting_at' => 'datetime',
        ];
    }
}
