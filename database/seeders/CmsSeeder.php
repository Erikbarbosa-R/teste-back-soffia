<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Post;
use App\Models\Tag;
use App\Models\Comment;
use Illuminate\Support\Facades\Hash;

class CmsSeeder extends Seeder
{
    public function run()
    {
        // Criar usuários administradores
        $adminUsers = [
            [
                'nome' => 'Admin CMS',
                'email' => 'admin@cms.com',
                'password' => Hash::make('admin123'),
                'telefone' => '(11) 99999-0000',
                'is_valid' => true,
            ],
            [
                'nome' => 'Editor Principal',
                'email' => 'editor@cms.com',
                'password' => Hash::make('editor123'),
                'telefone' => '(11) 88888-0000',
                'is_valid' => true,
            ]
        ];

        foreach ($adminUsers as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        // Criar usuários comuns
        $regularUsers = [
            [
                'nome' => 'Maria Silva',
                'email' => 'maria@example.com',
                'password' => Hash::make('123456'),
                'telefone' => '(11) 99999-1111',
                'is_valid' => true,
            ],
            [
                'nome' => 'João Santos',
                'email' => 'joao@example.com',
                'password' => Hash::make('123456'),
                'telefone' => '(11) 88888-2222',
                'is_valid' => true,
            ],
            [
                'nome' => 'Ana Costa',
                'email' => 'ana@example.com',
                'password' => Hash::make('123456'),
                'telefone' => '(11) 77777-3333',
                'is_valid' => true,
            ]
        ];

        foreach ($regularUsers as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        // Criar tags do CMS
        $tags = [
            ['name' => 'Tecnologia'],
            ['name' => 'Programação'],
            ['name' => 'Web Development'],
            ['name' => 'Mobile'],
            ['name' => 'Design'],
            ['name' => 'Marketing'],
            ['name' => 'Negócios'],
            ['name' => 'Tutorial'],
            ['name' => 'Notícias'],
            ['name' => 'Dicas']
        ];

        foreach ($tags as $tagData) {
            Tag::firstOrCreate($tagData);
        }

        // Criar posts de exemplo
        $admin = User::where('email', 'admin@cms.com')->first();
        $editor = User::where('email', 'editor@cms.com')->first();

        $posts = [
            [
                'title' => 'Bem-vindo ao CMS',
                'content' => 'Este é o primeiro post do nosso sistema de gerenciamento de conteúdo. Aqui você pode criar, editar e organizar todo o conteúdo do seu site.',
                'author_id' => $admin->id,
                'tags' => ['Notícias', 'Tecnologia']
            ],
            [
                'title' => 'Como usar o sistema CMS',
                'content' => 'Guia completo para usar todas as funcionalidades do nosso CMS. Aprenda a criar posts, gerenciar usuários e organizar conteúdo.',
                'author_id' => $editor->id,
                'tags' => ['Tutorial', 'Web Development']
            ],
            [
                'title' => 'Dicas de SEO para conteúdo',
                'content' => 'Aprenda as melhores práticas de SEO para otimizar seu conteúdo e melhorar o ranking nos mecanismos de busca.',
                'author_id' => $admin->id,
                'tags' => ['Marketing', 'Dicas', 'Web Development']
            ],
            [
                'title' => 'Tendências em desenvolvimento web',
                'content' => 'Descubra as principais tendências e tecnologias que estão moldando o futuro do desenvolvimento web em 2024.',
                'author_id' => $editor->id,
                'tags' => ['Tecnologia', 'Programação', 'Web Development']
            ],
            [
                'title' => 'Design responsivo: boas práticas',
                'content' => 'Como criar interfaces que funcionam perfeitamente em todos os dispositivos. Dicas essenciais para design responsivo.',
                'author_id' => $admin->id,
                'tags' => ['Design', 'Web Development', 'Mobile']
            ]
        ];

        foreach ($posts as $postData) {
            $tags = $postData['tags'];
            unset($postData['tags']);
            
            $post = Post::firstOrCreate(
                ['title' => $postData['title']],
                $postData
            );

            // Associar tags ao post
            foreach ($tags as $tagName) {
                $tag = Tag::where('name', $tagName)->first();
                if ($tag && !$post->tags()->where('tag_id', $tag->id)->exists()) {
                    $post->tags()->attach($tag->id);
                }
            }
        }

        // Criar alguns comentários
        $maria = User::where('email', 'maria@example.com')->first();
        $joao = User::where('email', 'joao@example.com')->first();
        $ana = User::where('email', 'ana@example.com')->first();

        $firstPost = Post::where('title', 'Bem-vindo ao CMS')->first();
        $tutorialPost = Post::where('title', 'Como usar o sistema CMS')->first();

        if ($firstPost && $maria) {
            Comment::firstOrCreate([
                'content' => 'Excelente sistema! Muito intuitivo e fácil de usar.',
                'post_id' => $firstPost->id,
                'user_id' => $maria->id,
            ]);
        }

        if ($tutorialPost && $joao) {
            Comment::firstOrCreate([
                'content' => 'Obrigado pelo tutorial! Ajudou muito a entender o sistema.',
                'post_id' => $tutorialPost->id,
                'user_id' => $joao->id,
            ]);
        }

        if ($firstPost && $ana) {
            Comment::firstOrCreate([
                'content' => 'Sistema muito bem estruturado. Parabéns pela iniciativa!',
                'post_id' => $firstPost->id,
                'user_id' => $ana->id,
            ]);
        }

        echo "CMS populado com sucesso!\n";
        echo "Usuários: " . User::count() . "\n";
        echo "Posts: " . Post::count() . "\n";
        echo "Tags: " . Tag::count() . "\n";
        echo "Comentários: " . Comment::count() . "\n";
    }
}
