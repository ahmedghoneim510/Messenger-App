<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'label',
        'last_message_id',
    ];

    public function participants() // users and conversation
    {
        return $this->belongsToMany(User::class, 'participants')
            ->withPivot([
                'role',
                'joined_at'
            ]);
    }
    public function messages()
    {
        return $this->hasMany(Message::class)->latest(); // from new to older
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function last_Message(){
        return $this->belongsTo(Message::class,'last_message_id','id')->withDefault();
    }

}
