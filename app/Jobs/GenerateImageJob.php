<?php

namespace App\Jobs;

use App\Models\Image;
use App\Models\Notification;
use App\Services\IndicatorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;

class GenerateImageJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Image $image,
        public int $scheduleId,
        public array $destinations,
        public bool $isResend = false,
    ) {}

    public function handle(IndicatorService $indicatorService): void
    {
        try {
            $this->image->load('customer');

            $lastUpdatedAt = $indicatorService->getLastUpdatedAtForImage($this->image->customer_id);

            $grid = $this->buildGrid($indicatorService);

            $html = view('images.index', [
                'customer' => $this->image->customer,
                'lastUpdatedAt' => $lastUpdatedAt,
                'image' => $this->image,
                'grid' => $grid,
            ])->render();

            $browsershot = Browsershot::html($html)
                ->windowSize(
                    str_starts_with($this->image->company, 'V') ? 900 : 1500,
                    0
                )
                ->deviceScaleFactor(2)
                ->fullPage()
                ->setOption('args', ['--disable-web-security']);

            if (config('app.env') === 'production') {
                $browsershot->setNodeBinary('/usr/bin/node')
                    ->setNpmBinary('/usr/bin/npm')
                    ->noSandbox();
            }

            $imageData = $browsershot->screenshot();

            $base64 = base64_encode($imageData);

            foreach ($this->destinations as $destination) {
                SendImageJob::dispatch(
                    $destination['value'],
                    $base64,
                    '.png',
                    $this->image->id,
                    $this->scheduleId
                )->delay(now()->addSeconds($destination['delay'] ?? 0));
            }

        } catch (\Exception $e) {
            Log::error('Generate Image Job Failed', [
                'image_id' => $this->image->id,
                'schedule_id' => $this->scheduleId,
                'error' => $e->getMessage(),
            ]);

            Notification::create([
                'type' => 'error',
                'title' => 'Erro ao Gerar Imagem',
                'message' => 'Falha ao gerar imagem: '.$this->image->name,
                'context' => [
                    'image_id' => $this->image->id,
                    'schedule_id' => $this->scheduleId,
                    'error' => $e->getMessage(),
                ],
            ]);

            throw $e;
        }
    }

    private function buildGrid(IndicatorService $indicatorService): array
    {
        $grid = [];
        $maxRow = 0;

        foreach ($this->image->indicators as $indicator) {
            $row = floor($indicator['position'] / 4);
            $maxRow = max($maxRow, $row);
        }

        foreach ($this->image->charts as $chart) {
            $maxRow = max($maxRow, $chart['row']);
        }

        for ($i = 0; $i <= $maxRow; $i++) {
            $grid[$i] = [
                ['type' => null],
                ['type' => null],
                ['type' => null],
                ['type' => null],
            ];
        }

        $indicatorsData = $indicatorService->getIndicatorsForImage(
            $this->image->indicators,
            $this->image->customer_id,
            $this->image->company
        );

        foreach ($indicatorsData as $indicator) {
            $row = floor($indicator['position'] / 4);
            $col = $indicator['position'] % 4;
            $grid[$row][$col] = [
                'type' => 'indicator',
                'data' => $indicator['data'],
            ];
        }

        $chartsData = $indicatorService->getChartsForImage(
            $this->image->charts,
            $this->image->customer_id,
            $this->image->company
        );

        foreach ($chartsData as $chart) {
            $row = $chart['row'];
            $col = $chart['col'];
            $grid[$row][$col] = [
                'type' => 'chart',
                'chart_type' => $chart['type'],
                'data' => $chart['data'],
            ];
        }

        return $grid;
    }
}
