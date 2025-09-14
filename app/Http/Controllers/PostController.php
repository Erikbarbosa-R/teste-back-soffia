<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Http\Requests\CommentRequest;
use App\Repositories\PostRepositoryInterface;
use App\Repositories\CommentRepositoryInterface;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    use ApiResponseTrait;

    protected $postRepository;
    protected $commentRepository;

    public function __construct(PostRepositoryInterface $postRepository, CommentRepositoryInterface $commentRepository)
    {
        $this->postRepository = $postRepository;
        $this->commentRepository = $commentRepository;
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

        $postData = $posts->through(function ($post) {
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

    public function show(string $id): JsonResponse
    {
        $post = $this->postRepository->find($id);

        if (!$post) {
            return $this->notFoundResponse('Post não encontrado.');
        }

        $comments = $this->commentRepository->findByPost($id);

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
            'comments' => $comments->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'user' => [
                        'id' => $comment->user->id,
                        'nome' => $comment->user->nome,
                        'email' => $comment->user->email,
                    ],
                    'created_at' => $comment->created_at,
                ];
            }),
        ]);
    }

    public function update(PostRequest $request, string $id): JsonResponse
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

    public function destroy(string $id): JsonResponse
    {
        $deleted = $this->postRepository->delete($id);

        if (!$deleted) {
            return $this->notFoundResponse('Post não encontrado.');
        }

        return $this->successResponse(null, 'Post removido com sucesso.');
    }

    public function addComment(CommentRequest $request, string $postId): JsonResponse
    {
        try {
            $commentData = $request->validated();
            $commentData['post_id'] = $postId;
            $commentData['user_id'] = Auth::id();

            $comment = $this->commentRepository->create($commentData);

            return $this->successResponse([
                'id' => $comment->id,
                'content' => $comment->content,
                'user' => [
                    'id' => $comment->user->id,
                    'nome' => $comment->user->nome,
                    'email' => $comment->user->email,
                ],
                'post_id' => $comment->post_id,
                'created_at' => $comment->created_at,
            ], 'Comentário adicionado com sucesso.', 201);

        } catch (\Exception $e) {
            return $this->errorResponse('Erro ao adicionar comentário.', 400);
        }
    }

    public function deleteComment(string $postId, string $commentId): JsonResponse
    {
        $deleted = $this->commentRepository->delete($commentId);

        if (!$deleted) {
            return $this->notFoundResponse('Comentário não encontrado.');
        }

        return $this->successResponse(null, 'Comentário removido com sucesso.');
    }
}