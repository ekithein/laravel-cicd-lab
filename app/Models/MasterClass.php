<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterClass extends Model
{
    protected $fillable = [
        'creativity_type_id',
        'master_id',
        'title',
        'description',
        'class_date',
        'start_time',
        'group_size',
        'price',
    ];

    public function creativityType(): BelongsTo
    {
        return $this->belongsTo(CreativityType::class);
    }

    public function master(): BelongsTo
    {
        return $this->belongsTo(User::class, 'master_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enrollments');
    }
}
