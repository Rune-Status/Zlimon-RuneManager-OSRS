<?php

namespace App\Skill;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Skill\Runecraft
 *
 * @property int $id
 * @property int $account_id
 * @property int $rank
 * @property int $level
 * @property int $xp
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Account $account
 * @method static \Illuminate\Database\Eloquent\Builder|Runecraft newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Runecraft newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Runecraft query()
 * @method static \Illuminate\Database\Eloquent\Builder|Runecraft whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Runecraft whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Runecraft whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Runecraft whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Runecraft whereRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Runecraft whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Runecraft whereXp($value)
 * @mixin \Eloquent
 */
class Runecraft extends Model
{
    protected $table = 'runecraft';

    protected $fillable = ['level'];

    protected $hidden = ['user_id'];

    public function account()
    {
        return $this->belongsTo(\App\Account::class);
    }
}
