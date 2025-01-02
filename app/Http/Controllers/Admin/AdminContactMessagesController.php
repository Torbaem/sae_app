<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminContactMessagesController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::orderBy('created_at', 'desc')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'name' => $message->name,
                    'email' => $message->email,
                    'message' => $message->message,
                    'status' => $message->status,
                    'is_read' => $message->is_read,
                    'created_at' => $message->created_at->format('d/m/Y H:i'),
                ];
            });

        return Inertia::render('Admin/Contact/Index', [
            'messages' => $messages
        ]);
    }

    public function markAsRead($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->update(['is_read' => true]);
        
        return redirect()->back();
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:nuevo,en_proceso,pendiente,completado,spam'
        ]);

        $message = ContactMessage::findOrFail($id);
        $message->update(['status' => $request->status]);
        
        return redirect()->back();
    }

    public function destroy($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->delete();
        
        return redirect()->back();
    }
}