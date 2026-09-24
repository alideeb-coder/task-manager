<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;

use function Laravel\Prompts\task;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $tasks = Task::where('user_id',$request->user()->id)->get();
        $tasks = $request->user()->tasks()
            ->when($request->has('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->has('search'), fn($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->paginate(15);
        return Response()->json($tasks);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $data = $request->only(['title', 'description', 'status']);
        $data['user_id'] = $request->user()->id;
        $task = Task::create($data);
        return response()->json($task, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return response()->json($task);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task->update($request->validated());
        return response()->json($task);
    }
    //     public function updated(Request $request, string $id)
    // {
    //     $task = Task::find($id);

    //     if (!$task) {
    //         return response()->json(['message' => 'Task not found'], 404);
    //     }

    //     // 2. عدّل الحقول واحد واحد (بس إذا انبعثوا)
    //     if ($request->has('title')) {
    //         $task->title = $request->input('title');
    //     }

    //     if ($request->has('description')) {
    //         $task->description = $request->input('description');
    //     }

    //     // 3. احفظ
    //     $task->save();

    //     // 4. ارجع النتيجة
    //     return response()->json($task);
    // }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $deleted = $task->delete();

        if (!$deleted) {
            return response()->json(['message' => 'Delete failed'], 500);
        }

        return response()->noContent(); //204
    }
}
