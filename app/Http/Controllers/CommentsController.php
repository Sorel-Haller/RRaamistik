<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentsController extends Controller
{
    /**
     * Salvestab uue kommentaari.
     */
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'content' => ['required', 'string', 'max:255'],
        ]);

        $post->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $request->content
        ]);
        return redirect()->back();

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:1000'],
        ]);

        dd($request);

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
       
    }
}