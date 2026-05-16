<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'master_class_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function masterClass(): BelongsTo
    {
        return $this->belongsTo(MasterClass::class);
    }
}
