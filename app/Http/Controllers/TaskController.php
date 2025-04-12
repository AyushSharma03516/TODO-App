<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    // Show the tasks page
    public function index()
    {
        $tasks = Task::all();
        return view('tasks.index', compact('tasks'));
    }

    // Add a new task
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        // Check for duplicate task
        if (Task::where('title', $request->title)->exists()) {
            return back()->with('error', 'Task already exists!');
        }

        // Create and save the new task
        $task = Task::create([
            'title' => $request->title,
            'is_done' => false,
        ]);

        return redirect()->route('home');
    }

    // Toggle the completion status of a task
    public function toggleDone($id)
    {
        $task = Task::findOrFail($id);
        $task->is_done = !$task->is_done;
        $task->save();

        return redirect()->route('home');
    }

    // Delete a task
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return redirect()->route('home');
    }
    public function uploadBackground(Request $request)
{
    // Validate the image file
    $request->validate([
        'background_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // max size 2MB
    ]);

    // Store the uploaded image
    if ($request->hasFile('background_image')) {
        $path = $request->file('background_image')->store('backgrounds', 'public');
        // Save the image path in the session to persist it
        session(['background_image' => $path]);
    }

    return redirect()->route('home');
}
}
