<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 *
 *
 * @property int $id
 * @property-read string $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Collection> $collections
 * @property-read int|null $collections_count
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereId($value)
 * @mixin \Eloquent
 */
class Category extends Model
{
    protected function slug(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => Str::slug($value),
        );
    }

    public function collections(): HasMany
    {
        return $this->hasMany(Collection::class);
    }

//    public function broadcast()
//    {
//        return $this->hasMany(Broadcast::class);
//    }
//
//    public function log()
//    {
//        return $this->hasMany(Log::class);
//    }
}
