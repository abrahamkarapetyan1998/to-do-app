<?php

namespace App\Services;

use App\Models\ToDo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Intervention\Image\Facades\Image;

class ToDoService
{

    /**
     * Retrieve todos for the authenticated user.
     *
     * @return Collection|JsonResponse
     */
    public function getUserTodos(): JsonResponse
    {
        $user = auth()->user();

        if ($user) {
            $toDos = ToDo::where('user_id', $user->id)->get();

            return response()->json($toDos);
        }

        return response()->json(['error' => 'User not authenticated'], 401);
    }

        public function createTodo(array $data)
        {
            $validator = Validator::make($data, [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'tags' => 'nullable|string|max:255',
                'image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5000',
            ]);

            if ($validator->fails()) {
                throw new \InvalidArgumentException($validator->errors()->first());
            }

            $data['user_id'] = Auth::user()->id;

            $todo = Todo::create($data);

            if (!empty($data['image'])) {
                $imageName = $data['image']->hashName();

                // Save the original image
                $data['image']->storeAs('public/images', $imageName);

                // Generate a cropped version of the image
                $image = Image::make(storage_path('app/public/images/' . $imageName));
                $image->fit(150, 150);
                $croppedImageName = 'cropped_' . $imageName;
                $image->save(storage_path('app/public/images/' . $croppedImageName));

                // Save the image names in the database
                $todo->image_url = $imageName;
                $todo->save();
            }

            // Store tags separately for the todo
            if (!empty($data['tags'])) {
                $tags = explode(',', $data['tags']);
                foreach ($tags as $tag) {
                    $tag = trim($tag);
                    $todo->tags()->create(['tag' => $tag]);
                }
            }
            $tags = $todo->tags()->pluck('tag')->toArray();

            return [
                'todo' => $todo,
                'tags' => $tags,
            ];
        }

    /**
     * Update a todo.
     *
     * @param int $id The ID of the todo.
     * @param array $data The data to update the todo.
     * @return array The updated todo and tags.
     * @throws \InvalidArgumentException If the validation fails or the todo is not found.
     */
    public function updateTodo(int $id, array $data): array
    {
        $validator = Validator::make($data, [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'tags' => 'nullable|string|max:255',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5000',
        ]);

        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->errors()->first());
        }

        $todo = Todo::find($id);

        if (!$todo) {
            throw new \InvalidArgumentException('Todo not found');
        }

        $todo->title = $data['title'];
        $todo->description = $data['description'];

        // Update tags
        $todo->tags()->delete(); // Remove existing tags
        if (!empty($data['tags'])) {
            $tags = explode(',', $data['tags']);
            foreach ($tags as $tag) {
                $tag = trim($tag);
                $todo->tags()->create(['tag' => $tag]);
            }
        }

        // Update image
        if (!empty($data['image'])) {
            $validator = Validator::make(['image' => $data['image']], [
                'image' => 'image|mimes:jpeg,png,jpg,gif|max:5000',
            ]);

            if ($validator->fails()) {
                throw new \InvalidArgumentException($validator->errors()->first());
            }

            $imageName = $data['image']->hashName();

            // Save the original image
            $data['image']->storeAs('public/images', $imageName);

            // Generate a cropped version of the image
            $image = Image::make(storage_path('app/public/images/' . $imageName));
            $image->fit(150, 150);
            $croppedImageName = 'cropped_' . $imageName;
            $image->save(storage_path('app/public/images/' . $croppedImageName));

            // Save the image names in the database
            $todo->image_url = $imageName;
        }

        $todo->save();

        return [
            'todo' => $todo,
            'tags' => $data['tags'] ?? "",
        ];
    }

    /**
     * Search todo items by tags.
     *
     * @param  string  $searchTerm
     * @return Collection
     */
    public function searchByTags(string $searchTerm): Collection
    {
        $todos = Todo::whereHas('tags', function ($query) use ($searchTerm) {
            $query->where('tag', 'like', '%' . $searchTerm . '%');
        })->get();

        return $todos;
    }

    /**
     * Delete a todo.
     *
     * @param  int  $id  The ID of the todo to delete.
     * @throws \InvalidArgumentException  If the todo is not found.
     * @return \Illuminate\Http\JsonResponse  A JSON response indicating the success of the deletion.
     */
    public function destroyTodo(int $id): JsonResponse
    {
        // Find the todo by ID
        $todo = Todo::find($id);

        if (!$todo) {
            throw new \InvalidArgumentException('Todo not found');
        }

        // Delete the associated image file, if it exists
        if ($todo->image_url) {
            Storage::delete('public/images/' . $todo->image_url);
            Storage::delete('public/images/cropped_' . $todo->image_url);
        }

        // Delete the todo
        $todo->delete();

        return response()->json('Todo deleted successfully', 200);
    }

}
