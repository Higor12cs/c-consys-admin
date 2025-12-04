<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with(['customer', 'executor', 'supervisor']);

        if ($request->filled('e')) {
            $query->where('executed_by', $request->e);
        }

        if ($request->filled('s')) {
            $query->where('supervised_by', $request->s);
        }

        return inertia('Tasks/Index', [
            'tasks' => $query->get(),
            'customers' => Customer::orderBy('name')->get(),
            'users' => User::orderBy('name')->get(),
            'filters' => [
                'e' => $request->e,
                's' => $request->s,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,waiting,in_progress,completed',
            'due_date' => 'nullable|date',
            'executed_by' => 'required|exists:users,id',
            'supervised_by' => 'required|exists:users,id',
        ], [
            'required' => 'O campo é obrigatório.',
            'exists' => 'Valor inválido selecionado.',
            'max' => 'O campo não pode ter mais de :max caracteres.',
            'in' => 'Valor inválido selecionado.',
            'date' => 'Data inválida.',
        ]);

        $validated['created_by'] = auth()->id();

        if ($validated['status'] === 'completed') {
            $validated['completed_at'] = now();
        }

        Task::create($validated);

        return redirect()->route('tasks.index', [
            'e' => $request->query('e'),
            's' => $request->query('s'),
        ]);
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'customer_id' => 'sometimes|required|exists:customers,id',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'priority' => 'sometimes|required|in:low,medium,high',
            'status' => 'sometimes|required|in:pending,waiting,in_progress,completed,finished',
            'due_date' => 'sometimes|nullable|date',
            'executed_by' => 'sometimes|required|exists:users,id',
            'supervised_by' => 'sometimes|required|exists:users,id',
        ], [
            'required' => 'O campo é obrigatório.',
            'exists' => 'Valor inválido selecionado.',
            'max' => 'O campo não pode ter mais de :max caracteres.',
            'in' => 'Valor inválido selecionado.',
            'date' => 'Data inválida.',
        ]);

        if (isset($validated['status']) && $validated['status'] === 'completed' && $task->status !== 'completed') {
            $validated['completed_at'] = now();
        }

        $task->update($validated);

        return redirect()->route('tasks.index', [
            'e' => $request->query('e'),
            's' => $request->query('s'),
        ]);
    }

    public function destroy(Request $request, Task $task)
    {
        $task->delete();

        return redirect()->route('tasks.index', [
            'e' => $request->query('e'),
            's' => $request->query('s'),
        ]);
    }

    public function finish(Request $request, Task $task)
    {
        if ($task->supervised_by !== auth()->id()) {
            abort(403, 'Apenas supervisores podem finalizar tarefas.');
        }

        if ($task->status !== 'completed') {
            abort(400, 'Apenas tarefas completadas podem ser finalizadas.');
        }

        $task->update([
            'status' => 'finished',
            'finished_at' => now(),
        ]);

        return redirect()->route('tasks.index', [
            'e' => $request->query('e'),
            's' => $request->query('s'),
        ]);
    }
}
