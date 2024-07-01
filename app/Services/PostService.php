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

            $post = Post::create([
                'user_id' => $userId,
                'dummy_post_id' => 0, 
            ]);

            $dummyPostResponse = Http::post("https://dummyjson.com/posts/add", [
                'userId' => $userId,
                'title' => $data['title'],
                'body' => $data['body'],
            ])->json();

            $post->dummy_post_id = $post->id;
            $post->save();

            return $post;
        });
    }

    public function updatePost(Post $post, array $data)
    {
        return DB::transaction(function () use ($post, $data) {
            $post->update($data);
            try {
                $response = Http::put("https://dummyjson.com/posts/{$post->dummy_post_id}", [
                    'title' => $data['title'],
                    'body' => $data['body'],
                ]);
    
                Log::info('API Response', ['response' => $response->json()]);
    
                if ($response->successful()) {
                    $responseData = $response->json();
                    Log::info('Пост API успешно обновлен', ['post_id' => $post->id]);
                    return [
                        'post' => $post,
                        'external' => [
                            'title' => $responseData['title'],
                            'body' => $responseData['body']
                        ],
                    ];
                } else {
                    Log::warning('Ошибка обновления внешнего API', ['post_id' => $post->id, 'response' => $response->json()]);
                    throw new \Exception('Ошибка обновления внешнего API');
                }
            } catch (\Exception $e) {
                Log::error('Ошибка обновления внешнего API', ['post_id' => $post->id, 'error' => $e->getMessage()]);
                throw $e; 
            }
        });
    }

    public function deletePost(Post $post)
    {
        $post->delete();

        Http::delete("https://dummyjson.com/posts/{$post->dummy_post_id}");
    }
}