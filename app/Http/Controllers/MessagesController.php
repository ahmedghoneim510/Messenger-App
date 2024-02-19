<?php

namespace App\Http\Controllers;

use App\Events\MessageCreated;
use App\Models\Conversation;
use App\Models\Recipient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MessagesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id) // id for conversation
    {
        $user = Auth::user();
        $conversation = $user->conversations()->findOrFail($id); // we 'll get user's conversation
        return $conversation->messages()->paginate();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'message' => ['required', 'string'],
            'conversation_id' => [
                Rule::requiredIf(fn() => !$request->has('user_id')),
                'int',
                'exists:conversations,id'
            ],
            'user_id' => [
                Rule::requiredIf(fn() => !$request->has('conversation_id')),
                'int',
                'exists:users,id'
            ],
        ]);
        $user = User::find(1);
        $conversation_id = $request->post('conversation_id');
        $user_id = $request->post('user_id');
        DB::beginTransaction();
        try {
            if ($conversation_id) {
                $conversation = $user->conversations()->findOrFail($conversation_id);
            } else {
                $conversation=Conversation::where('type','peer')
                    ->whereHas('participants',function ($builder) use($user_id,$user){
                         $builder->join('participants as participants2','participants2.conversation_id','participants.conversation_id')
                             ->where('participants.user_id',$user->id)
                             ->where('participants2.user_id',$user_id);
                    })->first();
                if(!$conversation){
                    $conversation=Conversation::create(['type'=>'peer','user_id'=>$user->id]);
                    $conversation->participants()->attach([
                        $user->id => ['joined_at' => now()],
                        $user_id => ['joined_at' => now()],
                    ]);
                }
            }
            $message = $conversation->messages()->create([
                'user_id' => $user->id,
                'body' => $request->post('message'),
                'type'=>'text'
            ]);

            // we 'll get user_id from all paticipants and add message to them
            DB::statement('INSERT INTO recipients (user_id,message_id)
            SELECT user_id,? FROM participants
                             where conversation_id=?', [$message->id, $conversation->id]);

            $conversation->update(['last_message_id'=>$message->id]);
            DB::commit();

           broadcast(new MessageCreated($message));
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
        return $message;



    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Recipient::where('user_id', Auth::id())
            ->where('message_id', $id)
            ->delete();
        return ['message'=>"Deleted successfully"];
    }
}
