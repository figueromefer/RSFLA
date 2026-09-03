<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentRollEntry extends Model
{
    protected $fillable = [
        'tenant_name', 'suite', 'lease_commencement_date', 'lease_expiration_date',
        'square_footage', 'lease_term', 'start_rent', 'rent_increases', 'free_rent',
        'is_vacant', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'square_footage' => 'integer',
            'lease_commencement_date' => 'date',
            'lease_expiration_date' => 'date',
            'is_vacant' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
