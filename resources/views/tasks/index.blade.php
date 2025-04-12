<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My To-Do List</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* Same styles as before */
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f5f5f5;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #67b26f, #4ca2cd);
        }

        .container {
            width: 100%;
            max-width: 600px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            box-sizing: border-box;
        }

        h1 {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 20px;
            color: #333;
        }

        .task-form {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .task-form input {
            width: 80%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        .task-form button {
            width: 15%;
            padding: 10px;
            border: none;
            background-color: #4caf50;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .task-form button:hover {
            background-color: #45a049;
        }

        .tasks-container {
            margin-top: 20px;
            max-height: 400px;
            overflow-y: auto;
        }

        .task {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            margin-bottom: 10px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }

        .task.done {
            background-color: #d4edda;
            text-decoration: line-through;
            color: #6c757d;
            opacity: 0.6;
            display: none; /* Hide completed tasks by default */
        }

        .task .title {
            flex-grow: 1;
            padding-right: 10px;
            font-size: 16px;
        }

        .task button {
            padding: 8px 12px;
            font-size: 14px;
            border: none;
            background-color: #dc3545;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .task button:hover {
            background-color: #c82333;
        }

        .task input[type="checkbox"] {
            margin-right: 15px;
        }

        .show-all-btn {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            text-align: center;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
            margin-top: 20px;
        }

        .show-all-btn:hover {
            background-color: #0056b3;
        }

        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            display: none;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
            z-index: 100;
        }

        .toast.error {
            background-color: #dc3545;
        }

        .toast.show {
            display: block;
            animation: fadeInOut 3s;
        }

        @keyframes fadeInOut {
            0% {
                opacity: 0;
                transform: translateX(30px);
            }
            50% {
                opacity: 1;
                transform: translateX(0);
            }
            100% {
                opacity: 0;
                transform: translateX(30px);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>My To-Do List</h1>

        <!-- Error Message Toast -->
        @if(session('error'))
            <div class="toast error show">{{ session('error') }}</div>
        @endif

        <!-- Add Task Form -->
        <form action="{{ route('tasks.store') }}" method="POST" class="task-form">
            @csrf
            <input type="text" name="title" placeholder="Add a new task..." required>
            <button type="submit">Add</button>
        </form>

        <!-- Task List -->
        <div class="tasks-container">
            @foreach ($tasks as $task)
                <div class="task {{ $task->is_done ? 'done' : '' }}" id="task-{{ $task->id }}">
                    <form action="{{ route('tasks.toggle', $task->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('PUT')
                        <input type="checkbox" name="is_done" onclick="toggleTaskCompletion({{ $task->id }}, this)" {{ $task->is_done ? 'checked' : '' }}>
                    </form>

                    <span class="title">{{ $task->title }}</span>

                    <!-- Delete Task Form -->
                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Are you sure you want to delete this task?')">Delete</button>
                    </form>
                </div>
            @endforeach
        </div>

        <!-- Show All Tasks Button -->
        <button class="show-all-btn" onclick="showAllTasks()">Show All Tasks</button>
    </div>

    <script>
        // Toggle task completion status (hide completed task and mark it as done)
        function toggleTaskCompletion(taskId, checkbox) {
            const taskElement = document.getElementById('task-' + taskId);
            const isChecked = checkbox.checked;

            // Update task completion in the backend
            const form = checkbox.closest('form');
            form.submit();

            // Hide task if completed
            if (isChecked) {
                taskElement.classList.add('done');
                taskElement.style.display = 'none';
            } else {
                taskElement.classList.remove('done');
                taskElement.style.display = 'flex';
            }
        }

        // Show all tasks (both completed and non-completed)
        function showAllTasks() {
            const tasks = document.querySelectorAll('.task');
            tasks.forEach(task => {
                task.style.display = 'flex'; // Ensure all tasks are visible
                task.classList.remove('done'); // Remove "done" class if previously added
            });
        }

        // Display toast notification for error or success
        const toast = document.querySelector('.toast');
        if (toast) {
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }
    </script>
</body>
</html>
