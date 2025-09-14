<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use App\Models\Tag;
use App\Models\Comment;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Dashboard",
 *     description="Estatísticas e métricas do CMS"
 * )
 */
class DashboardController extends Controller
{
    use ApiResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/dashboard/stats",
     *     summary="Estatísticas do CMS",
     *     description="Retorna estatísticas gerais do sistema",
     *     tags={"Dashboard"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Estatísticas retornadas com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Estatísticas obtidas com sucesso."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="users", type="object",
     *                     @OA\Property(property="total", type="integer", example=150),
     *                     @OA\Property(property="active", type="integer", example=120),
     *                     @OA\Property(property="inactive", type="integer", example=30)
     *                 ),
     *                 @OA\Property(property="posts", type="object",
     *                     @OA\Property(property="total", type="integer", example=45),
     *                     @OA\Property(property="published", type="integer", example=40),
     *                     @OA\Property(property="draft", type="integer", example=5)
     *                 ),
     *                 @OA\Property(property="tags", type="integer", example=25),
     *                 @OA\Property(property="comments", type="integer", example=200),
     *                 @OA\Property(property="recent_posts", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="popular_tags", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     )
     * )
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'users' => [
                'total' => User::count(),
                'active' => User::where('is_valid', true)->count(),
                'inactive' => User::where('is_valid', false)->count(),
            ],
            'posts' => [
                'total' => Post::count(),
                'published' => Post::count(), // Assumindo que todos os posts estão publicados
                'draft' => 0, // Implementar campo de status se necessário
            ],
            'tags' => Tag::count(),
            'comments' => Comment::count(),
            'recent_posts' => Post::with(['author', 'tags'])
                ->latest()
                ->limit(5)
                ->get()
                ->map(function ($post) {
                    return [
                        'id' => $post->id,
                        'title' => $post->title,
                        'author' => $post->author->nome,
                        'tags' => $post->tags->pluck('name'),
                        'created_at' => $post->created_at,
                    ];
                }),
            'popular_tags' => Tag::withCount('posts')
                ->orderBy('posts_count', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($tag) {
                    return [
                        'id' => $tag->id,
                        'name' => $tag->name,
                        'posts_count' => $tag->posts_count,
                    ];
                }),
        ];

        return $this->successResponse($stats, 'Estatísticas obtidas com sucesso.');
    }

    /**
     * @OA\Get(
     *     path="/api/dashboard/activity",
     *     summary="Atividade Recente",
     *     description="Retorna as atividades recentes do sistema",
     *     tags={"Dashboard"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Atividade recente retornada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Atividade recente obtida com sucesso."),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="type", type="string", example="post_created"),
     *                 @OA\Property(property="description", type="string", example="Novo post criado"),
     *                 @OA\Property(property="user", type="string", example="João Silva"),
     *                 @OA\Property(property="created_at", type="string", example="2023-01-01T00:00:00.000000Z")
     *             ))
     *         )
     *     )
     * )
     */
    public function activity(): JsonResponse
    {
        // Simular atividade recente (em um sistema real, isso viria de uma tabela de logs)
        $recentPosts = Post::with('author')
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($post) {
                return [
                    'type' => 'post_created',
                    'description' => "Post '{$post->title}' foi criado",
                    'user' => $post->author->nome,
                    'created_at' => $post->created_at,
                ];
            });

        $recentComments = Comment::with(['user', 'post'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($comment) {
                return [
                    'type' => 'comment_added',
                    'description' => "Comentário adicionado ao post '{$comment->post->title}'",
                    'user' => $comment->user->nome,
                    'created_at' => $comment->created_at,
                ];
            });

        $activity = $recentPosts->concat($recentComments)
            ->sortByDesc('created_at')
            ->values();

        return $this->successResponse($activity, 'Atividade recente obtida com sucesso.');
    }
}
