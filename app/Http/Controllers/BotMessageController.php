<?php

namespace App\Http\Controllers;

use App\Models\BotMessage;
use Illuminate\Http\Request;

class BotMessageController extends Controller
{
    public function index(Request $request)
    {
        $parentId = $request->get('parent_id');
        
        $query = BotMessage::query();
        
        if ($parentId) {
            $query->where('parent_id', $parentId);
            $parentMessage = BotMessage::find($parentId);
        } else {
            $query->whereNull('parent_id');
            $parentMessage = null;
        }

        $messages = $query->withCount('children')->get();

        return view('bot-messages.index', compact('messages', 'parentMessage', 'parentId'));
    }

    public function create(Request $request)
    {
        $parentId = $request->get('parent_id');
        $parentMessage = $parentId ? BotMessage::find($parentId) : null;
        $allMessages = BotMessage::all();
        
        return view('bot-messages.create', compact('parentId', 'parentMessage', 'allMessages'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'text' => 'required|string|max:255',
            'response' => 'nullable|string',
            'parent_id' => 'nullable|exists:bot_messages,id',
        ]);

        BotMessage::create($data);

        return redirect()->route('bot-messages.index', ['parent_id' => $request->parent_id])
            ->with('success', 'Bot message created successfully.');
    }

    public function edit(BotMessage $botMessage)
    {
        $allMessages = BotMessage::where('id', '!=', $botMessage->id)->get(); // Prevent setting itself as parent
        return view('bot-messages.edit', compact('botMessage', 'allMessages'));
    }

    public function update(Request $request, BotMessage $botMessage)
    {
        $data = $request->validate([
            'text' => 'required|string|max:255',
            'response' => 'nullable|string',
            'parent_id' => 'nullable|exists:bot_messages,id',
        ]);

        // Prevent setting itself or its children as parent to avoid infinite loops (simplified check)
        if ($request->parent_id == $botMessage->id) {
            return back()->withErrors(['parent_id' => 'A message cannot be its own parent.']);
        }

        $botMessage->update($data);

        return redirect()->route('bot-messages.index', ['parent_id' => $botMessage->parent_id])
            ->with('success', 'Bot message updated successfully.');
    }

    public function destroy(BotMessage $botMessage)
    {
        $parentId = $botMessage->parent_id;
        $botMessage->delete(); // This will cascade and delete all children if DB constraint is set correctly
        
        return redirect()->route('bot-messages.index', ['parent_id' => $parentId])
            ->with('success', 'Bot message and its sub-options deleted successfully.');
    }
}
