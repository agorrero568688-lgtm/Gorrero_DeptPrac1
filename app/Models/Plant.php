<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Plant extends Model
{
    use HasFactory;

    /**
     * Standard plant categories supported by the nursery system.
     */
    public const CATEGORIES = [
        'Ornamental',
        'Flowering',
        'Succulent',
        'Herb',
    ];

    /**
     * Allowed mass-assignable fields for Plant CRUD operations.
     */
    protected $fillable = [
        'user_id',
        'plant_name',
        'category',
        'unit_price',
        'description',
        'stock_status',
    ];

    /**
     * Cast unit_price to decimal with 2 decimal places.
     */
    protected $casts = [
        'unit_price' => 'decimal:2',
    ];

    /**
     * Relationship: A Plant belongs to a User (the administrator who created it).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Helper accessor to format stock status cleanly in views and reports.
     */
    public function getFormattedStatusAttribute(): string
    {
        return $this->stock_status === 'in_stock' ? 'In Stock' : 'Out of Stock';
    }
}