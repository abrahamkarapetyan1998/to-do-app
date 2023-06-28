<?php

use App\Http\Controllers\ToDoController;
use App\Services\ToDoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tests\TestCase;

class ToDoControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $todoService;
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->todoService = $this->createMock(ToDoService::class);
        $this->controller = new ToDoController($this->todoService);
    }

    public function testIndex()
    {
        $todos = [
            [
                'title' => 'Todo 3',
                'description' => 'Description of Todo 3с',
                'image_url' => 'https://example.com/todo2.jpg',
                'user_id' => 1,
            ],
        ];

        $this->todoService->expects($this->once())
            ->method('getUserTodos')
            ->willReturn(new JsonResponse($todos));

        $response = $this->controller->index();

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
    }

    public function testUpdate()
    {
        $id = 1;
        $requestData = [
            'title' => 'Updated Todo',
            'description' => 'Updated description',
            'tags' => 'tag1, tag2',
        ];

        $result = [
            'id' => 1,
            'title' => 'Updated Todo',
            'description' => 'Updated description',
            'image_url' => 'https://example.com/todo.jpg',
            'user_id' => 1,
            'created_at' => '2023-06-28 10:00:00',
            'updated_at' => '2023-06-28 11:00:00',
        ];

        $request = Request::create("/todos/{$id}", 'PUT', $requestData);

        $this->todoService->expects($this->once())
            ->method('updateTodo')
            ->with($id, $requestData)
            ->willReturn($result);

        $response = $this->controller->update($id, $request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(json_encode($result, JSON_PRETTY_PRINT), $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
    }

    public function testSearch()
    {
        $searchTerm = 'tag1';

        $results = collect([
            [
                'id' => 1,
                'title' => 'Todo 1',
                'description' => 'Description of Todo 1',
                'image_url' => 'https://example.com/todo1.jpg',
                'user_id' => 1,
                'created_at' => '2023-06-28 09:00:00',
                'updated_at' => '2023-06-28 09:00:00',
            ],
            [
                'id' => 3,
                'title' => 'Todo 3',
                'description' => 'Description of Todo 3',
                'image_url' => 'https://example.com/todo3.jpg',
                'user_id' => 1,
                'created_at' => '2023-06-28 11:00:00',
                'updated_at' => '2023-06-28 11:00:00',
            ],
        ]);

        $request = Request::create('/todos/search', 'POST', ['search' => $searchTerm]);

        $this->todoService->expects($this->once())
            ->method('searchByTags')
            ->with($searchTerm)
            ->willReturn($results);

        $response = $this->controller->search($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(json_encode($results->toArray(), JSON_PRETTY_PRINT), $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
    }

    public function testDestroy()
    {
        $todoId = 1;

        $this->todoService->expects($this->once())
            ->method('destroyTodo')
            ->with($todoId)
            ->willReturn(response()->json('Todo deleted successfully'));

        $response = $this->controller->destroy($todoId);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals('{"headers":{},"original":"Todo deleted successfully","exception":null}', $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
    }
}
