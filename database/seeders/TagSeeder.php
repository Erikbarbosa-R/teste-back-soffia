<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tags = [
            'organization',
            'planning',
            'collaboration',
            'writing',
            'calendar',
            'api',
            'json',
            'schema',
            'node',
            'github',
            'rest',
            'web',
            'framework',
            'http2',
            'https',
            'localhost',
            'organizing',
            'webapps',
            'domain',
            'developer',
            'proxy',
        ];

        foreach ($tags as $tag) {
            Tag::create(['name' => $tag]);
        }
    }
}




