<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>Laravel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.2/css/bootstrap.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <!-- Include Select2 JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Styles -->
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #BA4949 !important;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.2/css/bootstrap.min.css">
</head>
<body class="antialiased">
<div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0">
    <div class="container mx-auto px-4">
        <div class="flex justify-end items-center">
            @auth
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn position-absolute top-0 end-0">Logout</button>
                </form>
            @endauth
        </div>

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Create New Todo</h5>
                <form  method="POST" id="form-create">
                    @csrf
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" class="form-control" id="image" name="image">
                    </div>

                    <div class="mb-3">
                        <label for="tags" class="form-label">Tags</label>
                        <br>
                        <select type="text" id="tags-input" multiple="multiple" class="form-control" placeholder="Enter tags"></select>
                    </div>

                    <button type="submit" class="btn btn-primary">Create</button>
                </form>
            </div>
        </div>
    </div>
        <div class="card mt-4">
            <div class="card-body">
                <div class="mb-3" style="width: 25%;">
                    <input type="text" class="form-control" id="search" placeholder="Search by tags">
                </div>

                <h5 class="card-title">Todo List</h5>
                <table class="table">
                    <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Image</th>
                        <th>Tags</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody id="todo-table" class="table">
                    @foreach($todos as $todo)
                        <tr data-todo-id="{{$todo->id}}">
                            <td class="title-column">{{ $todo->title }}</td>
                            <td class="description-column">{{ $todo->description }}</td>
                            <td>
                                @if ($todo->image_url)
                                    <a class="img-link" href="{{ asset('storage/images/' . $todo->image_url) }}" target="_blank">
                                        <img class="img-preview" src="{{ asset('storage/images/cropped_' . $todo->image_url) }}" alt="Cropped Image" width="150">
                                    </a>
                                @else
                                    No Image
                                @endif
                            </td>
                            <td class="tags-column" style="width: 25%">
                                @foreach($todo->tags as $tag)
                                {{ $tag->tag}}
                                    @if(!$loop->last)
                                        ,
                                    @endif
                                @endforeach
                            </td>
                            <td>
                                <button class="btn btn-warning edit-button" data-todo-id="{{ $todo->id }}" data-bs-toggle="modal" data-bs-target="#editModal">Edit</button>
                                <button class="btn btn-danger delete-button" data-todo-id="{{ $todo->id }}" style="color: black">Delete</button>
                            </td>
                                <div class="modal fade" id="editModal-{{ $todo->id }}" tabindex="-1" aria-labelledby="editModalLabel-{{ $todo->id }}" aria-hidden="true">                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel">Edit Todo</h5>
                                            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('todos.update', $todo->id) }}" class="form-edit" data-todo-id="{{$todo->id}}">
                                                <input type="hidden" name="_method" value="PUT">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                                <div class="mb-3">
                                                    <label for="edit-title" class="form-label">Title</label>
                                                    <input type="text" class="form-control" id="edit-title" name="title" value="{{ $todo->title }}">
                                                </div>

                                                <div class="mb-3">
                                                    <label for="edit-description" class="form-label">Description</label>
                                                    <textarea class="form-control" id="edit-description" name="description">{{ $todo->description }}</textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="edit-image" class="form-label">Image</label>
                                                    <input type="file" class="form-control" id="edit-image" name="image" accept="image/*" value="{{$todo->image_url}}">

                                                    @if ($todo->image_url)
                                                        <a href="{{ asset('storage/images/' . $todo->image_url) }}" target="_blank">
                                                            <img src="{{ asset('storage/images/cropped_' . $todo->image_url) }}" alt="Cropped Image" width="150">
                                                        </a>
                                                    @else
                                                        <p>No image</p>
                                                    @endif
                                                </div>

                                                <div class="mb-3">
                                                    <label for="tags-input-edit-{{$todo->id}}" class="form-label">Tags</label>
                                                    <br>
                                                    <select class="form-control tags-input-edit" id="tags-input-edit-{{$todo->id}}" multiple="multiple" style="width: 100%;">
                                                        @foreach($todo->tags as $tag)
                                                            <option value="{{$tag->tag}}" selected>{{$tag->tag}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Are you sure you want to delete this todo?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button class="btn btn-danger delete-button" data-todo-id="{{ $todo->id }}" style="color: black">Delete</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
</div>
</div>
<div id="loader" class="loader-container">
    <div class="loader"></div>
</div>
<script>
    $(document).ready(function() {
        $('#tags-input').select2({
            placeholder: "Type The Tag And Then Press Enter",
            tags: true,
            tokenSeparators: [',', ' ']
        });
        $('.tags-input-edit').each(function() {
            $(this).select2({
                tags: true,
                width: '100%'
            });
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('#form-create').submit(function(event) {
            event.preventDefault(); // Prevent form submission

            var formData = new FormData($(this)[0]); // Get the form data
            var tags = $('#tags-input').val(); // Get the selected tags from Select2
            var loader = $('#loader');// Prevent form submission
            loader.show();
            // Append the selected tags to the formData
            formData.append('tags', tags);
            $.ajax({
                url: '{{ route('todos.store') }}', // Replace with your actual route
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    var row = '<tr data-todo-id="' + response.todo.id + '">' +
                        '<td>' + response.todo.title + '</td>' +
                        '<td>' + response.todo.description + '</td>' +
                        '<td>';

                    if (response.todo.image_url) {
                        row += '<a class="img-link" href="storage/images/' + response.todo.image_url + '" target="_blank">' +
                            '<img class="img-preview" src="storage/images/cropped_' + response.todo.image_url + '" alt="Cropped Image" width="150">' +
                            '</a>';
                    } else {
                        row += 'No Image';
                    }

                    row += '</td>' +
                        '<td>' + response.tags + '</td>' +
                        '<td>' +
                        '<button class="btn btn-warning edit-button" data-todo-id="' + response.todo.id + '" data-bs-toggle="modal" data-bs-target="#editModal">Edit</button>' +
                        '<button class="btn btn-danger delete-button" data-todo-id="' + response.todo.id + '" style="color: black">Delete</button>' +
                        '</td>' +
                        '</tr>';

                    $('#todo-table').append(row);
                    alert('Todo created successfully');

                    $('form')[0].reset();
                    $('#image-preview').attr('src', '{{ asset('storage/images/') }}/cropped_' + response.todo.image_url);

                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                },
                complete: function() {
                    // Hide the loader
                    loader.hide();
                }
            });
            $('#image-input').change(function() {
                var input = this;
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#image-preview').attr('src', e.target.result);
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            });
        });
    });
</script>
<script>
        $(document).ready(function() {
            $('.form-edit').submit(function(event) {
                event.preventDefault();
                var loader = $('#loader');// Prevent form submission
                loader.show();

                var todoId = $(this).data('todo-id');

                // Get the form data
                var formData = new FormData($(this)[0]);

                // Get the selected tags for the current form
                var tags = $('#tags-input-edit-' + todoId + ' option:selected').map(function() {
                    return $(this).val();
                }).get();

                // Append the selected tags to the formData
                formData.append('tags', tags);

                $.ajax({
                    url: '{{ route('todos.update', $todo->id ?? '')  }}',
                    type: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        var rows = $('tr[data-todo-id="' + todoId + '"]');
                        rows.each(function() {
                            var row = $(this);
                            row.find('.title-column').text(response.title);
                            row.find('.description-column').text(response.description);
                                row.find('.tags-column').html(response.tags);

                            if (response.todo.image_url) {
                                 $('.img-preview').attr('src', 'storage/images/cropped_' + response.todo.image_url).attr('alt', 'Cropped Image').attr('width', '150');
                                 $('.img-link').attr('href','storage/images/' + response.todo.image_url).attr('target', '_blank')
                            } else {
                                imageColumn.text('No Image');
                            }
                        });

                        $('#editModal-' + todoId).modal('hide');
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    },
                    complete: function() {
                        // Hide the loader
                        loader.hide();
                    }
                });
            });

            $('.edit-button').click(function() {
                var todoId = $(this).data('todo-id');
                $('#editModal-' + todoId).modal('show');
            });

            var searchTimer; // Variable to store the timer ID

            $('#search').on('keyup', function() {
                var searchTerm = $(this).val();

                clearTimeout($.data(this, 'searchTimer')); // Clear the previous timer

                if (searchTerm.trim() === '') {
                    // If the search term is empty, show all todos
                    $('#todo-table tr').show();
                } else {
                    // Set a timer to delay the search request
                    $(this).data('searchTimer', setTimeout(function() {
                        $.ajax({
                            url: '{{ route('todos.search') }}',
                            type: 'GET',
                            data: {
                                search: searchTerm
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                var matchedTodoIds = response.map(function(todo) {
                                    return todo.id;
                                });

                                // Hide all todos
                                $('#todo-table tr').hide();

                                // Show only the matched todos
                                matchedTodoIds.forEach(function(todoId) {
                                    $('#todo-table tr[data-todo-id="' + todoId + '"]').show();
                                });
                            },
                        });
                    }, 500)); // Delay the search request by 500 milliseconds (adjust as needed)
                }
            });
            $('.delete-button').click(function() {
                var todoId = $(this).data('todo-id');

                if (confirm('Are you sure you want to delete this todo?')) {
                    $.ajax({
                        url: '{{ route('todos.destroy', '') }}/' + todoId,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            alert('Todo deleted successfully');
                            $('tr[data-todo-id="' + todoId + '"]').remove();

                        },
                        error: function(xhr, status, error) {
                            console.log(xhr.responseText);
                        }
                    });
                }
            });
        });
</script>
<style>
    .loader-container {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999;
    }

    .loader {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        border: 16px solid #f3f3f3;
        border-top: 16px solid #3498db;
        border-radius: 50%;
        width: 120px;
        height: 120px;
        animation: spin 2s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
</style>
</body>
</html>
