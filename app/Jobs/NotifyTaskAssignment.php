<?php

namespace App\Jobs;

use App\Models\Task;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class NotifyTaskAssignment implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Task $task,
        public User $user,
        public string $role
    ) {}

    public function handle(WhatsAppService $whatsAppService): void
    {
        if (! $this->user->whatsapp) {
            Log::warning('User does not have WhatsApp number', [
                'user_id' => $this->user->id,
                'task_id' => $this->task->id,
            ]);

            return;
        }

        $message = $this->buildMessage();

        $result = $whatsAppService->sendText(
            $this->user->whatsapp,
            $message
        );

        if (! $result['success']) {
            Log::error('Failed to send WhatsApp notification', [
                'user_id' => $this->user->id,
                'task_id' => $this->task->id,
                'error' => $result['error'] ?? 'Unknown error',
            ]);
        }
    }

    private function buildMessage(): string
    {
        $creator = User::find($this->task->created_by);
        $customer = $this->task->customer;

        $roleText = $this->role === 'supervisor' ? 'supervisor' : 'executor';

        $priority = match ($this->task->priority) {
            'low' => 'Baixa',
            'medium' => 'Média',
            'high' => 'Alta',
            default => $this->task->priority,
        };

        $message = "🔔 *Nova Tarefa Atribuída*\n\n";
        $message .= "Você foi designado como *{$roleText}* de uma nova tarefa.\n\n";
        $message .= "📋 *Título:* {$this->task->title}\n";
        $message .= "👤 *Cliente:* {$customer->name}\n";
        $message .= "⚡ *Prioridade:* {$priority}\n";

        if ($this->task->due_date) {
            $dueDate = \Carbon\Carbon::parse($this->task->due_date);
            $message .= "📅 *Prazo:* {$dueDate->format('d/m/Y')}\n";
        }

        if ($this->task->description) {
            $message .= "\n📝 *Descrição:*\n{$this->task->description}\n";
        }

        $message .= "\n✍️ *Criado por:* {$creator->name}";

        return $message;
    }
}
