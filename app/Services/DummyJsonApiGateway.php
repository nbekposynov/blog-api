<?php 
namespace App\Services;

use Illuminate\Support\Facades\Http;

class DummyJsonApiGateway
{
    protected $apiUrl;

    public function __construct()
    {
        $this->apiUrl = config('services.dummyjson.url');
    }

    public function getPost($id)
    {
        return Http::get("{$this->apiUrl}/posts/{$id}")->json();
    }

    public function createPost(array $data)
    {
        return Http::post("{$this->apiUrl}/posts/add", $data)->json();
    }

    public function updatePost($id, array $data)
    {
        return Http::put("{$this->apiUrl}/posts/{$id}", $data);
    }

    public function deletePost($id)
    {
        return Http::delete("{$this->apiUrl}/posts/{$id}");
    }
}