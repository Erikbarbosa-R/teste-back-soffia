<?php

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface PostRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Post;
    public function create(array $data): Post;
    public function update(int $id, array $data): ?Post;
    public function delete(int $id): bool;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function findByTag(string $tag): Collection;
    public function search(string $query): Collection;
    public function searchPaginated(string $query, int $perPage = 15): LengthAwarePaginator;
    public function findByTagPaginated(string $tag, int $perPage = 15): LengthAwarePaginator;
}




