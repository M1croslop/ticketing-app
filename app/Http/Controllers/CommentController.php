<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Ticket $ticket)
    {

        $user = Auth::user();
        if ($user->role !== 'admin' && $user->role !== 'agent' && $user->id !== $ticket->user_id) {
            abort(403, 'No tienes permisos para comentar en este ticket.');
        }
        $request->validate([
            'body' => 'required|string|min:2'
        ]);

        $ticket->comments()->create([
            'body' => $request->body,
            'user_id' => $user->id,
        ]);

        return back()->with('success', 'Comentario agregado');
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket, Comment $comment)
    {
        $comment->delete();

        return back()->with('success', 'Comentario eliminado');
    }
}
