<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Salvestab uue kommentaari.
     */
    public function store(Request $request, Post $post)
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:1000'],
        ]);

        $request->validate([
            'content'=>'required|max:255'
        ]);
        $post->comments()->create([
            'user_id'=> $request->user()->id,
            'content'=>$request->content
        ]);

        return redirect()->back();
       
        Comment::create([
            'user_id' => $request->user()->id,
            'post_id' => $post->id,
            'content' => $validated['content'],
        ]);

        return redirect()->back()->with('success', 'Comment added!');
    }

    /**
     * Kustutamine (valikuline).
     */
    public function destroy(Comment $comment)
{
    $user = auth()->user();

    if (!$user || !$user->is_admin) {
        return back()->with('error', 'Ainult admin saab kustutada');
    }

    $comment->delete();

    return back()->with('success', 'Kommentaar kustutatud');
}
}