<?php

namespace App\Contracts;

use App\Models\Post;

interface PostInterface
{
    public function getPosts();

    public function createPost(array $data, $userId);

    public function updatePost(Post $post, array $data);

    public function deletePost(Post $post);
}
?>