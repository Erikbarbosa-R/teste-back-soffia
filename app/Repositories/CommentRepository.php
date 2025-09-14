<?php

namespace App\Repositories;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Collection;

class CommentRepository implements CommentRepositoryInterface
{
    protected $model;

    public function __construct(Comment $model)
    {
        $this->model = $model;
    }

    public function find(string $id): ?Comment
    {
        return $this->model->with(['user', 'post'])->find($id);
    }

    public function create(array $data): Comment
    {
        $comment = $this->model->create($data);
        return $comment->load(['user', 'post']);
    }

    public function delete(string $id): bool
    {
        $comment = $this->find($id);
        if ($comment) {
            return $comment->delete();
        }
        return false;
    }

    public function findByPost(string $postId): Collection
    {
        return $this->model->with(['user'])
            ->where('post_id', $postId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
