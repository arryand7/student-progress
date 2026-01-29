<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'description',
        'is_configurable',
    ];

    protected function casts(): array
    {
        return [
            'is_configurable' => 'boolean',
        ];
    }

    /**
     * Get the roles that have this permission.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    /**
     * Scope a query to only include configurable permissions.
     */
    public function scopeConfigurable($query)
    {
        return $query->where('is_configurable', true);
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeInCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
