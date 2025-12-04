<?php

namespace App\Observers;

use App\Jobs\NotifyTaskAssignment;
use App\Jobs\NotifyTaskCompleted;
use App\Models\Task;
use App\Models\User;

class TaskObserver
{
    public function created(Task $task): void
    {
        if ($task->created_by !== $task->supervised_by) {
            $supervisor = User::find($task->supervised_by);
            if ($supervisor) {
                NotifyTaskAssignment::dispatch($task, $supervisor, 'supervisor');
            }
        }

        if ($task->created_by !== $task->executed_by) {
            $executor = User::find($task->executed_by);
            if ($executor) {
                NotifyTaskAssignment::dispatch($task, $executor, 'executor');
            }
        }
    }

    public function updated(Task $task): void
    {
        if ($task->wasChanged('supervised_by')) {
            $newSupervisor = User::find($task->supervised_by);

            if ($newSupervisor && $task->supervised_by !== auth()->id()) {
                NotifyTaskAssignment::dispatch($task, $newSupervisor, 'supervisor');
            }
        }

        if ($task->wasChanged('status') && $task->status === 'completed' && $task->supervised_by !== auth()->id()) {
            $supervisor = User::find($task->supervised_by);

            if ($supervisor) {
                NotifyTaskCompleted::dispatch($task, $supervisor);
            }
        }
    }
}
