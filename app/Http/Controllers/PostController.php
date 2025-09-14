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

/**
 * @OA\Tag(
 *     name="Posts",
 *     description="CRUD de posts e comentários"
 * )
 */
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

    /**
     * @OA\Get(
     *     path="/api/posts",
     *     summary="Listar posts",
     *     description="Retorna uma lista paginada de posts com filtros opcionais",
     *     tags={"Posts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Número de itens por página",
     *         required=false,
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *     @OA\Parameter(
     *         name="tag",
     *         in="query",
     *         description="Filtrar posts por tag",
     *         required=false,
     *         @OA\Schema(type="string", example="node")
     *     ),
     *     @OA\Parameter(
     *         name="query",
     *         in="query",
     *         description="Buscar posts por título ou conteúdo",
     *         required=false,
     *         @OA\Schema(type="string", example="tutorial")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de posts retornada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Posts listados com sucesso."),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="string", example="uuid"),
     *                 @OA\Property(property="title", type="string", example="Título do Post"),
     *                 @OA\Property(property="author", type="object",
     *                     @OA\Property(property="id", type="string", example="uuid"),
     *                     @OA\Property(property="nome", type="string", example="João Silva"),
     *                     @OA\Property(property="telefone", type="string", example="11999999999"),
     *                     @OA\Property(property="email", type="string", example="usuario@example.com")
     *                 ),
     *                 @OA\Property(property="content", type="string", example="Conteúdo do post..."),
     *                 @OA\Property(property="tags", type="array", @OA\Items(type="string"), example={"node", "javascript"})
     *             )),
     *             @OA\Property(property="pagination", type="object")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/posts",
     *     summary="Criar post",
     *     description="Cria um novo post no sistema",
     *     tags={"Posts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","content","author"},
     *             @OA\Property(property="title", type="string", example="Título do Post"),
     *             @OA\Property(property="content", type="string", example="Conteúdo completo do post..."),
     *             @OA\Property(property="author", type="string", example="uuid-do-autor"),
     *             @OA\Property(property="tags", type="array", @OA\Items(type="string"), example={"node", "javascript"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Post criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Post criado com sucesso."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="string", example="uuid"),
     *                 @OA\Property(property="title", type="string", example="Título do Post"),
     *                 @OA\Property(property="author", type="object"),
     *                 @OA\Property(property="content", type="string", example="Conteúdo do post..."),
     *                 @OA\Property(property="tags", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     )
     * )
     */
    public function store(PostRequest $request): JsonResponse
    {
        try {
            $postData = $request->validated();
            $postData['author_id'] = $postData['author'];
            unset($postData['author']); // Remove o campo author do array

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
            return $this->errorResponse('Erro ao criar post: ' . $e->getMessage(), 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/posts/{id}",
     *     summary="Buscar post por ID",
     *     description="Retorna os dados de um post específico com seus comentários",
     *     tags={"Posts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do post",
     *         required=true,
     *         @OA\Schema(type="string", example="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Dados do post obtidos com sucesso."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="string", example="uuid"),
     *                 @OA\Property(property="title", type="string", example="Título do Post"),
     *                 @OA\Property(property="author", type="object"),
     *                 @OA\Property(property="content", type="string", example="Conteúdo do post..."),
     *                 @OA\Property(property="tags", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="comments", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post não encontrado"
     *     )
     * )
     */
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

    /**
     * @OA\Put(
     *     path="/api/posts/{id}",
     *     summary="Atualizar post",
     *     description="Atualiza os dados de um post existente",
     *     tags={"Posts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do post",
     *         required=true,
     *         @OA\Schema(type="string", example="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Título do Post"),
     *             @OA\Property(property="content", type="string", example="Conteúdo completo do post..."),
     *             @OA\Property(property="author", type="string", example="uuid-do-autor"),
     *             @OA\Property(property="tags", type="array", @OA\Items(type="string"), example={"node", "javascript"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Post atualizado com sucesso."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post não encontrado"
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/posts/{id}",
     *     summary="Deletar post",
     *     description="Remove um post do sistema",
     *     tags={"Posts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do post",
     *         required=true,
     *         @OA\Schema(type="string", example="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post removido com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Post removido com sucesso.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post não encontrado"
     *     )
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $deleted = $this->postRepository->delete($id);

        if (!$deleted) {
            return $this->notFoundResponse('Post não encontrado.');
        }

        return $this->successResponse(null, 'Post removido com sucesso.');
    }

    /**
     * @OA\Post(
     *     path="/api/posts/{post}/comments",
     *     summary="Adicionar comentário",
     *     description="Adiciona um comentário a um post específico",
     *     tags={"Posts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="post",
     *         in="path",
     *         description="ID do post",
     *         required=true,
     *         @OA\Schema(type="string", example="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"content"},
     *             @OA\Property(property="content", type="string", example="Este é um comentário sobre o post...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Comentário adicionado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Comentário adicionado com sucesso."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="string", example="uuid"),
     *                 @OA\Property(property="content", type="string", example="Este é um comentário..."),
     *                 @OA\Property(property="user", type="object"),
     *                 @OA\Property(property="post_id", type="string", example="uuid"),
     *                 @OA\Property(property="created_at", type="string", example="2023-01-01T00:00:00.000000Z")
     *             )
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/posts/{post}/comments/{comment}",
     *     summary="Deletar comentário",
     *     description="Remove um comentário de um post",
     *     tags={"Posts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="post",
     *         in="path",
     *         description="ID do post",
     *         required=true,
     *         @OA\Schema(type="string", example="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="comment",
     *         in="path",
     *         description="ID do comentário",
     *         required=true,
     *         @OA\Schema(type="string", example="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comentário removido com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Comentário removido com sucesso.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Comentário não encontrado"
     *     )
     * )
     */
    public function deleteComment(string $postId, string $commentId): JsonResponse
    {
        $deleted = $this->commentRepository->delete($commentId);

        if (!$deleted) {
            return $this->notFoundResponse('Comentário não encontrado.');
        }

        return $this->successResponse(null, 'Comentário removido com sucesso.');
    }
}