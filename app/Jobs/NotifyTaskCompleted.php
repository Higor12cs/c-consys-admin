<?php

namespace App\Jobs;

use App\Models\Task;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class NotifyTaskCompleted implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Task $task,
        public User $supervisor
    ) {}

    public function handle(WhatsAppService $whatsAppService): void
    {
        if (! $this->supervisor->whatsapp) {
            Log::warning('Supervisor does not have WhatsApp number', [
                'user_id' => $this->supervisor->id,
                'task_id' => $this->task->id,
            ]);

            return;
        }

        $message = $this->buildMessage();

        $result = $whatsAppService->sendText(
            $this->supervisor->whatsapp,
            $message
        );

        if (! $result['success']) {
            Log::error('Failed to send WhatsApp notification', [
                'user_id' => $this->supervisor->id,
                'task_id' => $this->task->id,
                'error' => $result['error'] ?? 'Unknown error',
            ]);
        }
    }

    private function buildMessage(): string
    {
        $executor = User::find($this->task->executed_by);
        $customer = $this->task->customer;

        $priority = match ($this->task->priority) {
            'low' => 'Baixa',
            'medium' => 'Média',
            'high' => 'Alta',
            default => $this->task->priority,
        };

        $message = "✅ *Tarefa Concluída*\n\n";
        $message .= "Uma tarefa sob sua supervisão foi concluída.\n\n";
        $message .= "📋 *Título:* {$this->task->title}\n";
        $message .= "👤 *Cliente:* {$customer->name}\n";
        $message .= "⚡ *Prioridade:* {$priority}\n";
        $message .= "👷 *Executado por:* {$executor->name}\n";

        if ($this->task->completed_at) {
            $completedDate = \Carbon\Carbon::parse($this->task->completed_at);
            $message .= "📅 *Concluída em:* {$completedDate->format('d/m/Y H:i')}\n";
        }

        if ($this->task->description) {
            $message .= "\n📝 *Descrição:*\n{$this->task->description}";
        }

        return $message;
    }
}
