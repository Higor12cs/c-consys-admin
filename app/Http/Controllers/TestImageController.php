<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateImageJob;
use App\Models\Image;
use App\Models\Schedule;
use App\Services\IndicatorService;

class TestImageController extends Controller
{
    public function preview(Image $image, IndicatorService $indicatorService)
    {
        $image->load('customer');

        $lastUpdatedAt = $indicatorService->getLastUpdatedAtForImage($image->customer_id);

        $grid = $this->buildGrid($image, $indicatorService);

        return view('images.index', [
            'customer' => $image->customer,
            'lastUpdatedAt' => $lastUpdatedAt,
            'image' => $image,
            'grid' => $grid,
        ]);
    }

    private function buildGrid(Image $image, IndicatorService $indicatorService): array
    {
        $grid = [];
        $maxRow = 0;

        foreach ($image->indicators as $indicator) {
            $row = floor($indicator['position'] / 4);
            $maxRow = max($maxRow, $row);
        }

        foreach ($image->charts as $chart) {
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
            $image->indicators,
            $image->customer_id,
            $image->company
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
            $image->charts,
            $image->customer_id,
            $image->company
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

    public function send(Image $image)
    {
        $schedule = Schedule::first();

        GenerateImageJob::dispatch($image, $schedule->id, [
            [
                'value' => '553434238524',
                'delay' => 0,
            ],
        ]);

        return response()->json(['message' => 'Image send job dispatched.']);
    }
}
