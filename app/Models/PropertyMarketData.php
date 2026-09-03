<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PropertyMarketData extends Model
{
    protected $table = 'property_market_data';

    protected $fillable = [
        'title',
        'image_path',
        'report_date',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'report_date' => 'date',
            'sort_order' => 'integer',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function getImageUrlAttribute(): string
    {
        if (Str::startsWith($this->image_path, ['http://', 'https://', '/storage/'])) {
            return $this->image_path;
        }

        return Storage::disk('public')->url($this->image_path);
    }

    public function hasLocalImage(): bool
    {
        return filled($this->image_path)
            && ! Str::startsWith($this->image_path, ['http://', 'https://', '/storage/']);
    }
}
