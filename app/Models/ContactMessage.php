<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactMessage extends Model
{
    use SoftDeletes;

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
        'is_read',
        'status',
        'assigned_to',
        'internal_notes',
        'last_contacted_at',
        'archived_at',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'last_contacted_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }

    public function lead(): HasOne
    {
        return $this->hasOne(Lead::class);
    }
}
