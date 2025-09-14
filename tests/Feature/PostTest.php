<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Support\Facades\Hash;

class PostTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->token = auth('api')->login($this->user);
    }

    public function test_can_list_posts()
    {
        Post::factory()->count(3)->create(['author_id' => $this->user->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/posts');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        '*' => [
                            'id',
                            'title',
                            'author' => [
                                'id',
                                'nome',
                                'telefone',
                                'email',
                            ],
                            'content',
                            'tags',
                        ],
                    ],
                    'pagination',
                ]);
    }

    public function test_can_create_post()
    {
        $postData = [
            'title' => 'Test Post',
            'content' => 'This is a test post content.',
            'author' => $this->user->id,
            'tags' => ['test', 'example'],
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/posts', $postData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        'id',
                        'title',
                        'author',
                        'content',
                        'tags',
                    ],
                ]);

        $this->assertDatabaseHas('posts', [
            'title' => 'Test Post',
            'content' => 'This is a test post content.',
            'author_id' => $this->user->id,
        ]);
    }

    public function test_can_show_post()
    {
        $post = Post::factory()->create(['author_id' => $this->user->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson("/api/posts/{$post->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        'id',
                        'title',
                        'author',
                        'content',
                        'tags',
                    ],
                ]);
    }

    public function test_can_update_post()
    {
        $post = Post::factory()->create(['author_id' => $this->user->id]);

        $updateData = [
            'title' => 'Updated Post Title',
            'content' => 'Updated content.',
            'author' => $this->user->id,
            'tags' => ['updated', 'test'],
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/posts/{$post->id}", $updateData);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        'id',
                        'title',
                        'author',
                        'content',
                        'tags',
                    ],
                ]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Post Title',
            'content' => 'Updated content.',
        ]);
    }

    public function test_can_delete_post()
    {
        $post = Post::factory()->create(['author_id' => $this->user->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson("/api/posts/{$post->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Post removido com sucesso.',
                ]);

        $this->assertDatabaseMissing('posts', [
            'id' => $post->id,
        ]);
    }

    public function test_can_filter_posts_by_tag()
    {
        $tag = Tag::create(['name' => 'test-tag']);
        $post = Post::factory()->create(['author_id' => $this->user->id]);
        $post->tags()->attach($tag->id);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/posts?tag=test-tag');

        $response->assertStatus(200);
    }

    public function test_can_search_posts()
    {
        Post::factory()->create([
            'title' => 'Searchable Post',
            'content' => 'This post contains searchable content.',
            'author_id' => $this->user->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/posts?query=searchable');

        $response->assertStatus(200);
    }
}


