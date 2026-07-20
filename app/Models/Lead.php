<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'contact_message_id',
        'name',
        'company',
        'email',
        'phone',
        'source',
        'service',
        'estimated_value',
        'probability',
        'sales_stage',
        'assigned_to',
        'next_follow_up_date',
        'notes',
        'activity_history',
    ];

    protected function casts(): array
    {
        return [
            'estimated_value' => 'decimal:2',
            'probability' => 'integer',
            'next_follow_up_date' => 'date',
            'activity_history' => 'array',
        ];
    }

    public function enquiry(): BelongsTo
    {
        return $this->belongsTo(ContactMessage::class, 'contact_message_id');
    }
}
