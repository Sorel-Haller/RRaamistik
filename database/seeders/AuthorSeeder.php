<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $postFactory = Post::factory(5)
            ->has(Comment::factory()->count(2), 'comments');

        Author::factory()->count(100)
        ->has(Post::factory(3), 'posts')
        ->create();
    }
}
