<?php

namespace App\Http\Controllers;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller {
   public function getMessages($receiver_id) {
    $my_id = Auth::id();

    $messages = Message::with(['sender:id,name', 'receiver:id,name']) 
        ->where(function($q) use ($my_id, $receiver_id) {
            $q->where('sender_id', $my_id)->where('receiver_id', $receiver_id);
        })
        ->orWhere(function($q) use ($my_id, $receiver_id) {
            $q->where('sender_id', $receiver_id)->where('receiver_id', $my_id);
        })
        ->orderBy('created_at', 'asc')
        ->get();

    return response()->json($messages);
}
    public function sendMessage(Request $request) {
        $msg = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message
        ]);
        return response()->json(['status' => 'success', 'data' => $msg]);
    }
}
