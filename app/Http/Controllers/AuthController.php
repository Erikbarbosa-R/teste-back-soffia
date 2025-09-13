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

    /**
     * Login user
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return $this->errorResponse('Credenciais inválidas. Verifique seu email e senha.', 401);
        }

        $user = Auth::user();

        return $this->successResponse([
            'user' => [
                'id' => $user->id,
                'nome' => $user->nome,
                'email' => $user->email,
                'telefone' => $user->telefone,
                'is_valid' => $user->is_valid,
            ],
            'token' => $token,
        ], 'Login realizado com sucesso.');
    }

    /**
     * Register new user
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $userData = $request->validated();
            $userData['password'] = Hash::make($userData['password']);

            $user = $this->userRepository->create($userData);

            $token = JWTAuth::fromUser($user);

            return $this->successResponse([
                'user' => [
                    'id' => $user->id,
                    'nome' => $user->nome,
                    'email' => $user->email,
                    'telefone' => $user->telefone,
                    'is_valid' => $user->is_valid,
                ],
                'token' => $token,
            ], 'Usuário registrado com sucesso.', 201);

        } catch (\Exception $e) {
            return $this->errorResponse('Erro ao cadastrar o usuário. Verifique os dados fornecidos.', 400);
        }
    }

    /**
     * Logout user
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return $this->successResponse(null, 'Logout realizado com sucesso.');
        } catch (\Exception $e) {
            return $this->errorResponse('Erro ao fazer logout.', 500);
        }
    }

    /**
     * Get authenticated user
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        $user = Auth::user();
        
        return $this->successResponse([
            'id' => $user->id,
            'nome' => $user->nome,
            'email' => $user->email,
            'telefone' => $user->telefone,
            'is_valid' => $user->is_valid,
        ]);
    }

    /**
     * Refresh token
     *
     * @return JsonResponse
     */
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




