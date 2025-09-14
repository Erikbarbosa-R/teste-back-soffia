<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Repositories\CommentRepositoryInterface;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    use ApiResponseTrait;

    protected $commentRepository;

    public function __construct(CommentRepositoryInterface $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function store(CommentRequest $request): JsonResponse
    {
        try {
            $commentData = $request->validated();
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
            ], 'Comentário criado com sucesso.', 201);

        } catch (\Exception $e) {
            return $this->errorResponse('Erro ao criar comentário.', 400);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        $deleted = $this->commentRepository->delete($id);

        if (!$deleted) {
            return $this->notFoundResponse('Comentário não encontrado.');
        }

        return $this->successResponse(null, 'Comentário removido com sucesso.');
    }
}
