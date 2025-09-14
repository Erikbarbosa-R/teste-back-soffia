<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="CMS API",
 *     version="1.0.0",
 *     description="Sistema de Gerenciamento de Conteúdo - API REST completa para gerenciamento de postagens com títulos, autores, conteúdos e tags",
 *     @OA\Contact(
 *         email="admin@example.com"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Servidor de Desenvolvimento"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 * 
 * @OA\Tag(
 *     name="Autenticação",
 *     description="Endpoints de autenticação JWT"
 * )
 * 
 * @OA\Tag(
 *     name="Usuários",
 *     description="CRUD de usuários"
 * )
 * 
 * @OA\Tag(
 *     name="Posts",
 *     description="CRUD de posts e comentários"
 * )
 * 
 * @OA\Tag(
 *     name="Tags",
 *     description="Gerenciamento de tags para categorização"
 * )
 * 
 * @OA\Tag(
 *     name="Dashboard",
 *     description="Estatísticas e métricas do CMS"
 * )
 * 
 * @OA\Tag(
 *     name="Utilitários",
 *     description="Endpoints utilitários da API"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}