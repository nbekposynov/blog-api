<?php

namespace App\Services;

use App\Contracts\PostInterface;
use App\Models\Post;
use App\Models\User;
use App\Repositories\PostRepository;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

    class PostService implements PostInterface
{
    protected $postRepository;
    protected $apiGateway;

    public function __construct(PostRepository $postRepository, DummyJsonApiGateway $apiGateway)
    {
        $this->postRepository = $postRepository;
        $this->apiGateway = $apiGateway;
    }

    public function getPosts()
    {
        $posts = $this->postRepository->paginate(10);
    
        $transformedPosts = $posts->getCollection()->transform(function ($post) {
            $dummyPost = $this->apiGateway->getPost($post->dummy_post_id);
    
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
        $post = $this->postRepository->create([
            'user_id' => $userId,
            'dummy_post_id' => 0, 
        ]);

        $dummyPostResponse = $this->apiGateway->createPost([
            'userId' => $userId,
            'title' => $data['title'],
            'body' => $data['body'],
        ]);

        $post->dummy_post_id = $post['id'];
        $post->save();

        return $post;
    }

    public function updatePost(Post $post, array $data)
    {
        $this->postRepository->update($post, $data);

            try {
                $response = $this->apiGateway->updatePost($post->dummy_post_id, [
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
        }

    public function deletePost(Post $post)
    {
        $this->postRepository->delete($post);
        $this->apiGateway->deletePost($post->dummy_post_id);
    }
}