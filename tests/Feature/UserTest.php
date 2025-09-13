<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user and authenticate
        $this->user = User::factory()->create();
        $this->token = auth('api')->login($this->user);
    }

    public function test_can_list_users()
    {
        User::factory()->count(3)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/users');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        '*' => [
                            'id',
                            'nome',
                            'email',
                            'telefone',
                            'is_valid',
                        ],
                    ],
                    'pagination',
                ]);
    }

    public function test_can_create_user()
    {
        $userData = [
            'nome' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'telefone' => '(11) 99999-9999',
            'is_valid' => true,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/users', $userData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        'id',
                        'nome',
                        'email',
                        'telefone',
                        'is_valid',
                    ],
                ]);

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'nome' => 'New User',
        ]);
    }

    public function test_can_show_user()
    {
        $user = User::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson("/api/users/{$user->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        'id',
                        'nome',
                        'email',
                        'telefone',
                        'is_valid',
                    ],
                ]);
    }

    public function test_can_update_user()
    {
        $user = User::factory()->create();

        $updateData = [
            'nome' => 'Updated Name',
            'email' => 'updated@example.com',
            'telefone' => '(11) 88888-8888',
            'is_valid' => false,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/users/{$user->id}", $updateData);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        'id',
                        'nome',
                        'email',
                        'telefone',
                        'is_valid',
                    ],
                ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'nome' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }

    public function test_can_delete_user()
    {
        $user = User::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Usuário removido com sucesso.',
                ]);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function test_returns_404_for_nonexistent_user()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/users/999');

        $response->assertStatus(404)
                ->assertJson([
                    'message' => 'Usuário não encontrado.',
                ]);
    }
}


