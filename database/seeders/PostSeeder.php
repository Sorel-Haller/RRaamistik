<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        Post::firstOrCreate(
            ['title' => 'Welcome to the Blog'],
            [
                'content'   => 'This is the first post on our blog. Stay tuned for more updates!',
                'published' => true,
                'author_id' => 1,
            ]
        );

        Post::factory()->count(20)->create();
    }
}