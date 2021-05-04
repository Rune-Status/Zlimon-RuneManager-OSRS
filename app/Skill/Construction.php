<?php

namespace App\Skill;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Skill\Construction
 *
 * @property int $id
 * @property int $account_id
 * @property int $rank
 * @property int $level
 * @property int $xp
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Account $account
 * @method static \Illuminate\Database\Eloquent\Builder|Construction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Construction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Construction query()
 * @method static \Illuminate\Database\Eloquent\Builder|Construction whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Construction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Construction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Construction whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Construction whereRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Construction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Construction whereXp($value)
 * @mixin \Eloquent
 */
class Construction extends Model
{
    protected $table = 'construction';

    protected $fillable = ['level'];

    protected $hidden = ['user_id'];

    public function account()
    {
        return $this->belongsTo(\App\Account::class);
    }
}
