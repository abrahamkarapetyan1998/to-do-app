<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\ToDoService;

class ToDoController extends Controller
{
    protected $userService;

    public function __construct(ToDoService $TodoService)
    {
        $this->ToDoService = $TodoService;
    }

    /*
     * Returns Users To Dos
     */
    public function index()
    {
        $todos = $this->ToDoService->getUserTodos();

        return view('welcome', compact('todos'));
    }

    /**
     * Creates To Do
     *
     * @param Request $request
     * @return JsonResponse
     *
     */
    public function store(Request $request):JsonResponse
    {
        $result = $this->ToDoService->createTodo($request->all());

        return response()->json($result, 200, ['Content-Type' => 'application/json'], JSON_PRETTY_PRINT);
    }

    /**
     * Update a todo item.
     *
     * @param  int  $id
     * @param  Request  $request
     * @return JsonResponse
     */
    public function update($id, Request $request): JsonResponse
    {
        $result = $this->ToDoService->updateTodo($id, $request->all());

        return response()->json($result, 200, ['Content-Type' => 'application/json'], JSON_PRETTY_PRINT);
    }

    /**
     * Search todo items by tags.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $searchTerm = $request->input('search');
        $results = $this->ToDoService->searchByTags($searchTerm);

        return response()->json($results, 200, ['Content-Type' => 'application/json'], JSON_PRETTY_PRINT);
    }

    /**
     * Delete a todo.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $result = $this->ToDoService->destroyTodo($id);

        return response()->json($result, 200);
    }
}
