<?php

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PostRepository implements PostRepositoryInterface
{
    protected $model;

    public function __construct(Post $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->with(['author', 'tags'])->get();
    }

    public function find(int $id): ?Post
    {
        return $this->model->with(['author', 'tags'])->find($id);
    }

    public function create(array $data): Post
    {
        $post = $this->model->create($data);
        
        if (isset($data['tags']) && is_array($data['tags'])) {
            $this->syncTags($post, $data['tags']);
        }
        
        return $post->load(['author', 'tags']);
    }

    public function update(int $id, array $data): ?Post
    {
        $post = $this->find($id);
        if ($post) {
            $post->update($data);
            
            if (isset($data['tags']) && is_array($data['tags'])) {
                $this->syncTags($post, $data['tags']);
            }
            
            return $post->fresh(['author', 'tags']);
        }
        return null;
    }

    public function delete(int $id): bool
    {
        $post = $this->find($id);
        if ($post) {
            return $post->delete();
        }
        return false;
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['author', 'tags'])->paginate($perPage);
    }

    public function findByTag(string $tag): Collection
    {
        return $this->model->with(['author', 'tags'])
            ->withTag($tag)
            ->get();
    }

    public function search(string $query): Collection
    {
        return $this->model->with(['author', 'tags'])
            ->search($query)
            ->get();
    }

    public function searchPaginated(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['author', 'tags'])
            ->search($query)
            ->paginate($perPage);
    }

    public function findByTagPaginated(string $tag, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['author', 'tags'])
            ->withTag($tag)
            ->paginate($perPage);
    }

    private function syncTags(Post $post, array $tags): void
    {
        $tagIds = [];
        foreach ($tags as $tagName) {
            $tag = \App\Models\Tag::firstOrCreate(['name' => $tagName]);
            $tagIds[] = $tag->id;
        }
        $post->tags()->sync($tagIds);
    }
}





