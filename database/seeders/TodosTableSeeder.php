<?php

namespace Database\Seeders;

use App\Models\ToDo;
use Illuminate\Database\Seeder;

class TodosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $todos = [
            [
                'title' => 'Todo 1',
                'description' => 'Description of Todo 1',
                'image_url' => 'https://example.com/todo1.jpg',
                'user_id' => 1,
            ],
            [
                'title' => 'Todo 2',
                'description' => 'Description of Todo 2',
                'image_url' => 'https://example.com/todo2.jpg',
                'user_id' => 1,
            ],
        ];

        foreach ($todos as $todo) {
            ToDo::create($todo);
        }
    }
}
