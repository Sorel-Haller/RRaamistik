<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;

class CommentsController extends Controller
{
    /**
     * Display all comments for a specific post.
     *
     * @param Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Post $post)
    {
        // Fetch comments for the post, newest first
        $comments = $post->comments()->latest()->get();

        return response()->json([
            'post_id' => $post->id,
            'comments' => $comments
        ]);
    }

    /**
     * Store a new comment for a specific post.
     *
     * @param Request $request
     * @param Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, Post $post)
    {
        // Validate incoming request
        $validated = $request->validate([
            'author' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // Create comment tied to the post
        $comment = $post->comments()->create($validated);

        return response()->json([
            'message' => 'Comment added successfully.',
            'comment' => $comment
        ], 201);
    }
}
