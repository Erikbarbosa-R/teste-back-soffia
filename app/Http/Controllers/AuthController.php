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
                return $this->errorResponse('Credenciais inválidas.', 401);
            }

            $user = JWTAuth::user();

            return $this->successResponse([
                'user' => [
                    'id' => $user->id,
                    'nome' => $user->nome,
                    'email' => $user->email,
                    'telefone' => $user->telefone,
                    'is_valid' => $user->is_valid,
                ],
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60
            ], 'Login realizado com sucesso.');

        } catch (\Exception $e) {
            return $this->errorResponse('Erro interno do servidor.', 500);
        }
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        \Log::info('=== REGISTER REQUEST START ===');
        \Log::info('Request data:', $request->all());
        
        try {
            \Log::info('Validating request...');
            $userData = $request->validated();
            \Log::info('Request validated successfully:', $userData);
            
            \Log::info('Hashing password...');
            $userData['password'] = Hash::make($userData['password']);
            \Log::info('Password hashed');

            \Log::info('Creating user via repository...');
            $user = $this->userRepository->create($userData);
            \Log::info('User created successfully:', ['id' => $user->id, 'email' => $user->email]);

            \Log::info('=== REGISTER SUCCESS ===');
            return $this->successResponse([
                'user' => [
                    'id' => $user->id,
                    'nome' => $user->nome,
                    'email' => $user->email,
                    'telefone' => $user->telefone,
                    'is_valid' => $user->is_valid,
                ],
                'message' => 'Usuário registrado com sucesso. Faça login para obter o token de acesso.'
            ], 'Usuário registrado com sucesso.', 201);

        } catch (\Exception $e) {
            \Log::error('=== REGISTER ERROR ===');
            \Log::error('Error message:', ['message' => $e->getMessage()]);
            \Log::error('Error trace:', ['trace' => $e->getTraceAsString()]);
            \Log::error('Request data:', $request->all());
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
            // Usar JWTAuth diretamente para evitar problemas com o AuthManager
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