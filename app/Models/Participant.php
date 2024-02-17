<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Participant extends Pivot
{
    use HasFactory;

    public $timestamps = false;

    protected $casts = [
        'joint_at' => 'datetime', // return as datetime
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);

    }

    public function user()
    {
        return $this->belongsTo(User::class);

    }
}
