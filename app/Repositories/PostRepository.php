<?php

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Support\Facades\DB;

class PostRepository
{
    public function paginate($perPage = 10)
    {
        return Post::paginate($perPage);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            return Post::create($data);
        });
    }

public function update(Post $post, array $data)
{
    return DB::transaction(function () use ($post, $data) {
        if (isset($data['dummy_post_id'])) {
            $post->dummy_post_id = $data['dummy_post_id'];
        }
        $post->update($data);
        return $post;
    });
}

    public function delete(Post $post)
    {
        return DB::transaction(function () use ($post) {
            return $post->delete();
        });
    }

    public function find($id)
    {
        return Post::find($id);
    }
}