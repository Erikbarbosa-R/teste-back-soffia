<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Tags",
 *     description="Gerenciamento de tags para categorização de conteúdo"
 * )
 */
class TagController extends Controller
{
    use ApiResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/tags",
     *     summary="Listar tags",
     *     description="Retorna uma lista de todas as tags disponíveis",
     *     tags={"Tags"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de tags retornada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tags listadas com sucesso."),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="string", example="uuid"),
     *                 @OA\Property(property="name", type="string", example="Laravel"),
     *                 @OA\Property(property="posts_count", type="integer", example=5)
     *             ))
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $tags = Tag::withCount('posts')->get();
        
        return $this->successResponse($tags, 'Tags listadas com sucesso.');
    }

    /**
     * @OA\Post(
     *     path="/api/tags",
     *     summary="Criar tag",
     *     description="Cria uma nova tag no sistema",
     *     tags={"Tags"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="JavaScript")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tag criada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tag criada com sucesso."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="string", example="uuid"),
     *                 @OA\Property(property="name", type="string", example="JavaScript")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dados inválidos"
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name'
        ]);

        $tag = Tag::create($request->only('name'));

        return $this->successResponse($tag, 'Tag criada com sucesso.', 201);
    }

    /**
     * @OA\Get(
     *     path="/api/tags/{id}",
     *     summary="Buscar tag por ID",
     *     description="Retorna os dados de uma tag específica com posts associados",
     *     tags={"Tags"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da tag",
     *         required=true,
     *         @OA\Schema(type="string", example="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tag encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tag encontrada."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="string", example="uuid"),
     *                 @OA\Property(property="name", type="string", example="Laravel"),
     *                 @OA\Property(property="posts", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tag não encontrada"
     *     )
     * )
     */
    public function show(string $id): JsonResponse
    {
        $tag = Tag::with('posts')->find($id);

        if (!$tag) {
            return $this->notFoundResponse('Tag não encontrada.');
        }

        return $this->successResponse($tag, 'Tag encontrada.');
    }

    /**
     * @OA\Put(
     *     path="/api/tags/{id}",
     *     summary="Atualizar tag",
     *     description="Atualiza os dados de uma tag existente",
     *     tags={"Tags"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da tag",
     *         required=true,
     *         @OA\Schema(type="string", example="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="JavaScript")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tag atualizada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tag atualizada com sucesso."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tag não encontrada"
     *     )
     * )
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $tag = Tag::find($id);

        if (!$tag) {
            return $this->notFoundResponse('Tag não encontrada.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name,' . $id
        ]);

        $tag->update($request->only('name'));

        return $this->successResponse($tag, 'Tag atualizada com sucesso.');
    }

    /**
     * @OA\Delete(
     *     path="/api/tags/{id}",
     *     summary="Deletar tag",
     *     description="Remove uma tag do sistema",
     *     tags={"Tags"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da tag",
     *         required=true,
     *         @OA\Schema(type="string", example="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tag removida com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tag removida com sucesso.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tag não encontrada"
     *     )
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $tag = Tag::find($id);

        if (!$tag) {
            return $this->notFoundResponse('Tag não encontrada.');
        }

        $tag->delete();

        return $this->successResponse(null, 'Tag removida com sucesso.');
    }
}
