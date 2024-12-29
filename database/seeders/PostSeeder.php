<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Profile;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = Post::factory(30)->create();
        $tags = Tag::all();
        $profiles = Profile::all();

        foreach ($posts as $post) {
            $tagIds = $tags->random(fake()->numberBetween(1, 5))->pluck('id');
            $post->tags()->attach($tagIds);
        }

//        foreach ($profiles as $profile) {
//            $postIds = $posts->random(fake()->numberBetween(1, 5))->pluck('id');
//            $profile->likedPosts()->attach($postIds);
//            $postIds = $posts->random(fake()->numberBetween(1, 5))->pluck('id');
//            $profile->likedPosts()->attach($postIds);
//        }
    }
}
