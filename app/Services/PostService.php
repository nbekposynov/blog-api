<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


    class PostService
{
    public function getPosts()
    {
        $posts = Post::paginate(10);
    
        $transformedPosts = $posts->getCollection()->transform(function ($post) {
            $dummyPost = Http::get("https://dummyjson.com/posts/{$post->dummy_post_id}")->json();
    
            $body = Str::limit($dummyPost['body'], 128);
    
            $user = User::find($post->user_id);
            $authorName = $user ? $user->name : 'Неизвестный автор';
    
            return [
                'id' => $post->id,
                'title' => $dummyPost['title'],
                'author_name' => $authorName,
                'body' => $body,
            ];
        });
    
        return response()->json($transformedPosts);
    }

    public function createPost(array $data, $userId)
    {
        return DB::transaction(function () use ($data, $userId) {
            // Сначала создаем пост локально
            $post = Post::create([
                'user_id' => $userId,
                'dummy_post_id' => 0, // временное значение
                'title' => $data['title'],
                'body' => $data['body'],
            ]);

            // Создаем пост на стороне API и получаем dummy_post_id
            $dummyPostResponse = Http::post("https://dummyjson.com/posts/add", [
                'userId' => $userId, // предполагаем, что API принимает userId
                'title' => $data['title'],
                'body' => $data['body'],
            ])->json();

            // Обновляем dummy_post_id в локальной базе данных
            $post->dummy_post_id = $post->id;
            $post->save();

            return $post;
        });
    }

    public function updatePost(Post $post, array $data)
    {
        return DB::transaction(function () use ($post, $data) {
            // Обновляем пост локально
            $post->update($data);

            // Опционально обновляем пост на стороне API
            $response = Http::put("https://dummyjson.com/posts/{$post->dummy_post_id}", [
                'title' => $data['title'],
                'body' => $data['body'],
            ]);

            // Логирование ответа
            Log::info("Updated dummy post", ['response' => $response->json()]);

            return $post;
        });
    }

    public function deletePost(Post $post)
    {
        // Удаляем пост локально
        $post->delete();

        // Опционально удаляем пост на стороне API
        Http::delete("https://dummyjson.com/posts/{$post->dummy_post_id}");
    }
}