<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Repositories\UserRepositoryInterface;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ApiResponseTrait;

    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of users
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $users = $this->userRepository->paginate($perPage);

        $userData = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'nome' => $user->nome,
                'email' => $user->email,
                'telefone' => $user->telefone,
                'is_valid' => $user->is_valid,
            ];
        });

        return $this->paginatedResponse($userData, 'Usuários listados com sucesso.');
    }

    /**
     * Store a newly created user
     *
     * @param UserRequest $request
     * @return JsonResponse
     */
    public function store(UserRequest $request): JsonResponse
    {
        try {
            $userData = $request->validated();
            
            if (isset($userData['password'])) {
                $userData['password'] = Hash::make($userData['password']);
            }

            $user = $this->userRepository->create($userData);

            return $this->successResponse([
                'id' => $user->id,
                'nome' => $user->nome,
                'email' => $user->email,
                'telefone' => $user->telefone,
                'is_valid' => $user->is_valid,
            ], 'Usuário criado com sucesso.', 201);

        } catch (\Exception $e) {
            return $this->errorResponse('Erro ao criar usuário.', 400);
        }
    }

    /**
     * Display the specified user
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            return $this->notFoundResponse('Usuário não encontrado.');
        }

        return $this->successResponse([
            'id' => $user->id,
            'nome' => $user->nome,
            'email' => $user->email,
            'telefone' => $user->telefone,
            'is_valid' => $user->is_valid,
        ]);
    }

    /**
     * Update the specified user
     *
     * @param UserRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UserRequest $request, int $id): JsonResponse
    {
        try {
            $userData = $request->validated();
            
            if (isset($userData['password'])) {
                $userData['password'] = Hash::make($userData['password']);
            }

            $user = $this->userRepository->update($id, $userData);

            if (!$user) {
                return $this->notFoundResponse('Usuário não encontrado.');
            }

            return $this->successResponse([
                'id' => $user->id,
                'nome' => $user->nome,
                'email' => $user->email,
                'telefone' => $user->telefone,
                'is_valid' => $user->is_valid,
            ], 'Usuário atualizado com sucesso.');

        } catch (\Exception $e) {
            return $this->errorResponse('Erro ao atualizar usuário.', 400);
        }
    }

    /**
     * Remove the specified user
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->userRepository->delete($id);

        if (!$deleted) {
            return $this->notFoundResponse('Usuário não encontrado.');
        }

        return $this->successResponse(null, 'Usuário removido com sucesso.');
    }
}




