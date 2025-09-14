<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Repositories\UserRepositoryInterface;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use ApiResponseTrait;

    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->only('email', 'password');
            
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'message' => 'Credenciais inválidas. Verifique seu email e senha.'
                ], 401);
            }

            $user = JWTAuth::user();

            return response()->json([
                'message' => 'Login realizado com sucesso.',
                'user' => [
                    'id' => $user->id,
                    'nome' => $user->nome,
                    'email' => $user->email,
                    'telefone' => $user->telefone,
                    'is_valid' => $user->is_valid,
                ],
                'token' => $token
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse('Erro interno do servidor.', 500);
        }
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $userData = $request->validated();
            
            $existingUser = $this->userRepository->findByEmail($userData['email']);
            if ($existingUser) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => [
                        'email' => [
                            'Este email já está cadastrado em nossa base de dados. Por favor, use outro email ou faça login.'
                        ]
                    ]
                ], 422);
            }
            
            $userData['password'] = Hash::make($userData['password']);
            $user = $this->userRepository->create($userData);

            $token = JWTAuth::fromUser($user);

            return response()->json([
                'message' => 'Usuário registrado com sucesso.',
                'user' => [
                    'id' => $user->id,
                    'nome' => $user->nome,
                    'email' => $user->email,
                    'telefone' => $user->telefone,
                    'is_valid' => $user->is_valid,
                ],
                'token' => $token
            ], 201);

        } catch (\Exception $e) {
            return $this->errorResponse('Erro ao cadastrar o usuário. Verifique os dados fornecidos.', 400);
        }
    }

    public function logout(): JsonResponse
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return $this->successResponse(null, 'Logout realizado com sucesso.');
        } catch (\Exception $e) {
            return $this->errorResponse('Erro ao fazer logout.', 500);
        }
    }

    public function me(): JsonResponse
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return $this->errorResponse('Usuário não autenticado.', 401);
            }

            return $this->successResponse([
                'id' => $user->id,
                'nome' => $user->nome,
                'email' => $user->email,
                'telefone' => $user->telefone,
                'is_valid' => $user->is_valid,
            ], 'Dados do usuário obtidos com sucesso.');

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return $this->errorResponse('Token expirado.', 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return $this->errorResponse('Token inválido.', 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return $this->errorResponse('Token não fornecido.', 401);
        } catch (\Exception $e) {
            return $this->errorResponse('Erro interno do servidor: ' . $e->getMessage(), 500);
        }
    }

    public function refresh(): JsonResponse
    {
        try {
            $token = JWTAuth::refresh(JWTAuth::getToken());
            return $this->successResponse(['token' => $token], 'Token renovado com sucesso.');
        } catch (\Exception $e) {
            return $this->errorResponse('Erro ao renovar token.', 500);
        }
    }
}