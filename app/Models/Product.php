<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'stock',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class);
    }

    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when($filters['category_id'] ?? null, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($filters['min_price'] ?? null, function ($query, $minPrice) {
                $query->where('price', '>=', $minPrice);
            })
            ->when($filters['max_price'] ?? null, function ($query, $maxPrice) {
                $query->where('price', '<=', $maxPrice);
            })
            ->when($filters['min_stock'] ?? null, function ($query, $minStock) {
                $query->where('stock', '>=', $minStock);
            })
            ->when($filters['max_stock'] ?? null, function ($query, $maxStock) {
                $query->where('stock', '<=', $maxStock);
            });
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucwords($value),
            set: fn (string $value) => trim($value),
        );
    }
}
