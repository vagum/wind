<?php

namespace App\Console\Commands;

use App\Events\Post\StoredPostEvent;
use App\Events\Tag\BeforeStoreTagEvent;
use App\Events\Tag\StoredTagEvent;
use App\Mail\Comment\StoredCommentMail;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Image;
use App\Models\Post;
use App\Models\Profile;
use App\Models\Role;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class GoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'go';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::first();
        Mail::to($user)->send(new StoredCommentMail());

// --------- с обсервером -----------

          // create
//        Post::factory()->create();

          // retrieved
//        Post::find(10);

          // update
//        $post = Post::find(62);
//        $post->update(['title' => 'Some new title 62']);

          // или можно так update делать, чтобы не было ошибки если поста нет
//          optional(Post::find(61))->update(['title' => 'Some new title 61']);

          // delete
//        $post = Post::find(62);
//        $post->delete();

// --------- с трейтом через boot -----

//        // create
//        User::factory()->create();
//
//        // retrieved
//        User::find(1);
//
//        // update
//        $user = User::find(12);
//        $user->update(['email' => 'new_email2@mail.ru']);
//
//        // или можно так update делать, чтобы не было ошибки если юзеря нет
//        optional(User::find(101))->update(['email' => 'new_101@mail.com']);
//
//        // delete
//        $user = User::find(12);
//        $user->delete();

// --------- с трейтом через HasLogBooted -----

//        // create
//        Profile::factory()->create();
//
//        // retrieved
//          Profile::find(1);
//
//        // update
//        $profile = Profile::find(12);
//        $profile->update(['phone' => '+1-111-1111111']);
//
//        // или можно так update делать, чтобы не было ошибки если профиля нет
//        optional(Profile::find(101))->update(['phone' => '+1-111-1111111']);
//
//        // delete
//        $profile = Profile::find(10);
//        $profile->delete();

// --------- с трейтом через HasLogBooted но с дополнительным create в boot -----

        // create
//        Category::factory()->create();
//
//        // retrieved
//        Category::find(1);
//
//        // update
//        $category = Category::find(12);
//        $category->update(['title' => 'New Category']);
//
//        // или можно так update делать, чтобы не было ошибки если профиля нет
//        optional(Category::find(101))->update(['title' => 'New Category']);
//
        // delete
//        $category = Category::find(10);
//        $category->delete();

// --------- Евент через слушателя -----
        // Слушатель на таги
//        BeforeStoreTagEvent::dispatch();
//        $tag = Tag::factory()->create();
//        StoredTagEvent::dispatch($tag);
//
//        $post = Post::factory()->create();
//        Log::channel('post')->info('this is my {id} bla bla', ['id' => $post->id]);
    }
}
