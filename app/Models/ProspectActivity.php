<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProspectActivity extends Model
{
    use HasFactory;

    public const TYPE_NOTE = 'note';
    public const TYPE_EMAIL = 'email';
    public const TYPE_CALL = 'call';
    public const TYPE_TOUR = 'tour';
    public const TYPE_PROPOSAL = 'proposal';
    public const TYPE_LEASE = 'lease';
    public const TYPE_STATUS_CHANGE = 'status_change';
    public const TYPE_CREATED = 'created';
    public const TYPE_UPDATED = 'updated';

    protected $fillable = [
        'prospect_id',
        'property_id',
        'user_id',
        'team_member_id',
        'type',
        'status_from',
        'status_to',
        'subject',
        'body',
        'occurred_at',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function prospect(): BelongsTo
    {
        return $this->belongsTo(Prospect::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function teamMember(): BelongsTo
    {
        return $this->belongsTo(TeamMember::class);
    }
}
