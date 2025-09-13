<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Repositories\PostRepositoryInterface;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    use ApiResponseTrait;

    protected $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $tag = $request->get('tag');
        $query = $request->get('query');

        if ($tag) {
            $posts = $this->postRepository->findByTagPaginated($tag, $perPage);
        } elseif ($query) {
            $posts = $this->postRepository->searchPaginated($query, $perPage);
        } else {
            $posts = $this->postRepository->paginate($perPage);
        }

        $postData = $posts->map(function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'author' => [
                    'id' => $post->author->id,
                    'nome' => $post->author->nome,
                    'telefone' => $post->author->telefone,
                    'email' => $post->author->email,
                ],
                'content' => $post->content,
                'tags' => $post->tags->pluck('name')->toArray(),
            ];
        });

        return $this->paginatedResponse($postData, 'Posts listados com sucesso.');
    }

    public function store(PostRequest $request): JsonResponse
    {
        try {
            $postData = $request->validated();
            $postData['author_id'] = $postData['author'];

            $post = $this->postRepository->create($postData);

            return $this->successResponse([
                'id' => $post->id,
                'title' => $post->title,
                'author' => [
                    'id' => $post->author->id,
                    'nome' => $post->author->nome,
                    'telefone' => $post->author->telefone,
                    'email' => $post->author->email,
                ],
                'content' => $post->content,
                'tags' => $post->tags->pluck('name')->toArray(),
            ], 'Post criado com sucesso.', 201);

        } catch (\Exception $e) {
            return $this->errorResponse('Erro ao criar post.', 400);
        }
    }

    public function show(int $id): JsonResponse
    {
        $post = $this->postRepository->find($id);

        if (!$post) {
            return $this->notFoundResponse('Post não encontrado.');
        }

        return $this->successResponse([
            'id' => $post->id,
            'title' => $post->title,
            'author' => [
                'id' => $post->author->id,
                'nome' => $post->author->nome,
                'telefone' => $post->author->telefone,
                'email' => $post->author->email,
            ],
            'content' => $post->content,
            'tags' => $post->tags->pluck('name')->toArray(),
        ]);
    }

    public function update(PostRequest $request, int $id): JsonResponse
    {
        try {
            $postData = $request->validated();
            
            if (isset($postData['author'])) {
                $postData['author_id'] = $postData['author'];
                unset($postData['author']);
            }

            $post = $this->postRepository->update($id, $postData);

            if (!$post) {
                return $this->notFoundResponse('Post não encontrado.');
            }

            return $this->successResponse([
                'id' => $post->id,
                'title' => $post->title,
                'author' => [
                    'id' => $post->author->id,
                    'nome' => $post->author->nome,
                    'telefone' => $post->author->telefone,
                    'email' => $post->author->email,
                ],
                'content' => $post->content,
                'tags' => $post->tags->pluck('name')->toArray(),
            ], 'Post atualizado com sucesso.');

        } catch (\Exception $e) {
            return $this->errorResponse('Erro ao atualizar post.', 400);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->postRepository->delete($id);

        if (!$deleted) {
            return $this->notFoundResponse('Post não encontrado.');
        }

        return $this->successResponse(null, 'Post removido com sucesso.');
    }
}