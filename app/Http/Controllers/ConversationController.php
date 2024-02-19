<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    public function index()
    {
        $user=Auth::user();
        return $user->conversations()->paginate(); // from pavit table
    }
    public function participants(Conversation $conversation){
        return $conversation->participants()->paginate();
    }
    public function AddParticipants(Request $request, Conversation $conversation){
        $request->validate([
            'user_id'=>'required|int|exists:users,id'
        ]);
        $conversation->participants()->attach($request->post('user_id'),[
            'joined_at'=>now(),
        ]);

    }
    public function RemoveParticipants(Request $request, Conversation $conversation){ // use urlencoded to use it
        $request->validate([
            'user_id'=>'required|int|exists:users,id'
        ]);
        $conversation->participants()->detach($request->post('user_id')); // detach used to remove the record from pivot table


    }
}
