<?php

namespace App\Http\Controllers;

use App\Contracts\PostInterface;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Services\PostService;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
class PostController extends Controller
{
    protected $postService;
    public function __construct(PostInterface $postService)
    {
        $this->postService = $postService;
    }

    public function index()
    {
        try {
            $posts = $this->postService->getPosts();
            return response()->json($posts);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ошибка при выводе данных', 'details' => $e->getMessage()], 500);
        }
    }

    public function store(StorePostRequest $request)
    {
        try {
            $user = Auth::user();
            $posts = $request->validated()['posts']; 
            $createdPosts = [];
    
            foreach ($posts as $postData) {
                $createdPosts[] = $this->postService->createPost($postData, $user->id);
            }
    
            return response()->json($createdPosts, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ошибка при создании поста', 'details' => $e->getMessage()], 500);
        }
    }

    public function update(UpdatePostRequest $request, $id)
    {
        try {
            $postsData = $request->validated()['posts']; 
            $updatedPosts = [];
            
            $post = Post::find($id);
    
            if (!$post) {
                return response()->json(['error' => 'Пост не найден'], 404);
            }
    
            $this->authorize('update', $post);
    
            foreach ($postsData as $postData) {
                $result = $this->postService->updatePost($post, $postData);
                $updatedPosts[] = [
                    'message' => 'Пост успешно обновлен',
                    'post' => $result['post'],
                    'external' => $result['external'],
                ];
            }
    
            return response()->json($updatedPosts, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ошибка редактирования', 'details' => $e->getMessage()], 500);
        }
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        try {
            $this->postService->deletePost($post);
            return response()->json(['message' => 'Пост удален']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ошибка при удалении'], 500);
        }
    }
}