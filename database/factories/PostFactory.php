<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Post;
use App\Models\Profile;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'profile_id' => Profile::inRandomOrder()->first()->id,
            'title' => fake()->realTextBetween(20,150),
            'category_id' => Category::inRandomOrder()->first()->id,
            'image_path' => fake()->imageUrl,
            'description' => fake()->realTextBetween(100,300),
            'content' => fake()->realTextBetween(400,1200),
            'published_at' => fake()->dateTime,
            'is_published' => fake()->boolean,
//            'views' => fake()->numberBetween(1,1000), // не нужно т.к. добавил в PostResource подсчет реальный
        ];
    }

//    public function configure(): self
//    {
//        return $this->afterCreating(function (Post $post) {
//
//            // Привязываем случайные теги к посту
//            $tags = Tag::inRandomOrder()->take(rand(1, 5))->pluck('id');
//            foreach ($tags as $tagId) {
//                PostTag::create([
//                    'post_id' => $post->id,
//                    'tag_id' => $tagId,
//                ]);
//            }
//
//            // Добавляем лайки
//            $profilesForLikes = Profile::inRandomOrder()->take(rand(1, 10))->pluck('id');
//            foreach ($profilesForLikes as $profileId) {
//                PostProfileLikes::create([
//                    'post_id' => $post->id,
//                    'profile_id' => $profileId,
//                ]);
//            }
//
//            // Добавляем просмотры
//            $profilesForViews = Profile::inRandomOrder()->take(rand(1, 15))->pluck('id');
//            foreach ($profilesForViews as $profileId) {
//                PostProfileViews::create([
//                    'post_id' => $post->id,
//                    'profile_id' => $profileId,
//                ]);
//            }
//        });
//    }
}
