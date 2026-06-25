<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prospect extends Model
{
    use HasFactory;

    public const STATUS_PROSPECT = 'prospect';
    public const STATUS_LEAD = 'lead';
    public const STATUS_TOUR_SCHEDULED = 'tour_scheduled';
    public const STATUS_TOUR_COMPLETED = 'tour_completed';
    public const STATUS_PROPOSAL_SENT = 'proposal_sent';
    public const STATUS_PROPOSAL_ACCEPTED = 'proposal_accepted';
    public const STATUS_LEASE_SIGNED = 'lease_signed';
    public const STATUS_INACTIVE = 'inactive';

    public const STATUSES = [
        self::STATUS_PROSPECT,
        self::STATUS_LEAD,
        self::STATUS_TOUR_SCHEDULED,
        self::STATUS_TOUR_COMPLETED,
        self::STATUS_PROPOSAL_SENT,
        self::STATUS_PROPOSAL_ACCEPTED,
        self::STATUS_LEASE_SIGNED,
        self::STATUS_INACTIVE,
    ];

    public const STATUS_LABELS = [
        self::STATUS_LEAD => 'New Lead',
        self::STATUS_PROSPECT => 'Active Prospect',
        self::STATUS_TOUR_SCHEDULED => 'Tour',
        self::STATUS_TOUR_COMPLETED => 'Tour',
        self::STATUS_PROPOSAL_SENT => 'Proposal',
        self::STATUS_PROPOSAL_ACCEPTED => 'Proposal',
        self::STATUS_LEASE_SIGNED => 'Lease',
        self::STATUS_INACTIVE => 'Inactive',
    ];

    public const STATUS_FORM_LABELS = [
        self::STATUS_LEAD => 'New Lead',
        self::STATUS_PROSPECT => 'Active Prospect',
        self::STATUS_TOUR_SCHEDULED => 'Tour - Scheduled',
        self::STATUS_TOUR_COMPLETED => 'Tour - Completed',
        self::STATUS_PROPOSAL_SENT => 'Proposal - Sent',
        self::STATUS_PROPOSAL_ACCEPTED => 'Proposal - Accepted',
        self::STATUS_LEASE_SIGNED => 'Lease',
        self::STATUS_INACTIVE => 'Inactive',
    ];

    protected $fillable = [
        'property_id',
        'assigned_team_member_id',
        'suite',
        'tenant',
        'use_type',
        'timing',
        'rsf',
        'broker',
        'contact_name',
        'first_name',
        'last_name',
        'email',
        'phone',
        'company',
        'source',
        'status',
        'budget',
        'desired_move_in',
        'last_contacted_at',
        'is_active',
        'notes',
        'visible_to_client',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'budget' => 'decimal:2',
            'desired_move_in' => 'date',
            'last_contacted_at' => 'datetime',
            'is_active' => 'boolean',
            'visible_to_client' => 'boolean',
            'rsf' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function assignedTeamMember(): BelongsTo
    {
        return $this->belongsTo(TeamMember::class, 'assigned_team_member_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ProspectActivity::class)->latest('occurred_at');
    }

    public function scopeVisibleToClient($query)
    {
        return $query->where('visible_to_client', true);
    }

    public function getFullNameAttribute(): string
    {
        return $this->tenant ?: trim($this->first_name.' '.($this->last_name ?? ''));
    }

    public static function statusLabel(string $status): string
    {
        return self::STATUS_LABELS[$status] ?? str($status)->replace('_', ' ')->title()->toString();
    }

    public static function statusFormLabel(string $status): string
    {
        return self::STATUS_FORM_LABELS[$status] ?? self::statusLabel($status);
    }
}
