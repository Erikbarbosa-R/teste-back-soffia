<?php

namespace App\Repositories;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Collection;

interface CommentRepositoryInterface
{
    public function find(string $id): ?Comment;
    public function create(array $data): Comment;
    public function delete(string $id): bool;
    public function findByPost(string $postId): Collection;
}
