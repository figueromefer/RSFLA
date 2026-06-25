<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyLink extends Model
{
    use HasFactory;

    public const TYPE_DROPBOX = 'dropbox';
    public const TYPE_BROADCAST_EMAIL = 'broadcast_email';
    public const TYPE_BROCHURE = 'brochure';
    public const TYPE_FILE = 'file';
    public const TYPE_URL = 'url';

    protected $fillable = [
        'property_id',
        'label',
        'type',
        'url',
        'description',
        'is_visible_to_client',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_visible_to_client' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function scopeVisibleToClient($query)
    {
        return $query->where('is_visible_to_client', true);
    }
}
