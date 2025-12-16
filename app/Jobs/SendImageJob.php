<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Models\ScheduleExecutionLog;
use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendImageJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $contactId,
        public string $base64,
        public string $extension,
        public int $imageId,
        public int $scheduleId,
        public bool $isResend = false,
    ) {}

    public function handle(WhatsAppService $whatsAppService): void
    {
        try {
            $result = $whatsAppService->sendImage(
                $this->contactId,
                $this->base64,
                $this->extension
            );

            if($this->isResend) {
                Log::info('Resending image via WhatsApp', [
                    'image_id' => $this->imageId,
                    'schedule_id' => $this->scheduleId,
                    'contact_id' => $this->contactId,
                    'status' => $result['success'] ? 'success' : 'failed',
                ]);
            }

            if ($result['success']) {
                ScheduleExecutionLog::create([
                    'image_id' => $this->imageId,
                    'schedule_id' => $this->scheduleId,
                    'execution_date' => now()->toDateTimeString(),
                    'status' => $this->isResend ? 'resent' : 'success',
                ]);
            } else {
                ScheduleExecutionLog::create([
                    'image_id' => $this->imageId,
                    'schedule_id' => $this->scheduleId,
                    'execution_date' => now()->toDateTimeString(),
                    'status' => 'failed',
                    'error_message' => $result['error'] ?? 'Unknown error',
                ]);

                Notification::create([
                    'type' => 'error',
                    'title' => 'Erro ao Enviar Imagem',
                    'message' => 'Falha ao enviar imagem via WhatsApp',
                    'context' => [
                        'image_id' => $this->imageId,
                        'schedule_id' => $this->scheduleId,
                        'contact_id' => $this->contactId,
                        'error' => $result['error'] ?? 'Unknown error',
                    ],
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Send Image Job Failed', [
                'image_id' => $this->imageId,
                'schedule_id' => $this->scheduleId,
                'contact_id' => $this->contactId,
                'error' => $e->getMessage(),
            ]);

            ScheduleExecutionLog::create([
                'image_id' => $this->imageId,
                'schedule_id' => $this->scheduleId,
                'execution_date' => now()->toDateTimeString(),
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            Notification::create([
                'type' => 'error',
                'title' => 'Erro ao Enviar Imagem',
                'message' => 'Exceção ao enviar imagem via WhatsApp',
                'context' => [
                    'image_id' => $this->imageId,
                    'schedule_id' => $this->scheduleId,
                    'contact_id' => $this->contactId,
                    'error' => $e->getMessage(),
                ],
            ]);

            throw $e;
        }
    }
}
