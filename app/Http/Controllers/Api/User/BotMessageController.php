<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\BotMessage;
use Illuminate\Http\Request;

class BotMessageController extends Controller
{
    /**
     * Get the root level bot messages (initial options).
     */
    public function start()
    {
        $messages = BotMessage::whereNull('parent_id')->get(['id', 'text']);
        return response()->json(['options' => $messages]);
    }

    /**
     * Get the response and children options for a specific message.
     */
    public function select($id)
    {
        $message = BotMessage::with('children:id,parent_id,text')->find($id);

        if (!$message) {
            return response()->json(['message' => 'Option not found'], 404);
        }

        return response()->json([
            'id' => $message->id,
            'text' => $message->text, // The option the user selected
            'response' => $message->response, // The bot's reply text (if any)
            'options' => $message->children // The next options for the user to select
        ]);
    }
}
