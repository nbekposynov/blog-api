<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Services\PostService;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    protected $postService;

    public function __construct(PostService $postService)
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
            $post = $this->postService->createPost($request->validated(), $user->id);
            return response()->json($post, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ошибка при создании поста', 'details' => $e->getMessage()], 500);
        }
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $this->authorize('update', $post);

        try {
            $post = $this->postService->updatePost($post, $request->validated());
            return response()->json($post);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ошибка редактирования'], 500);
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