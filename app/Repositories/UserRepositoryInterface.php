<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function all(): Collection;
    public function find(string $id): ?User;
    public function create(array $data): User;
    public function update(string $id, array $data): ?User;
    public function delete(string $id): bool;
    public function findByEmail(string $email): ?User;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
}





