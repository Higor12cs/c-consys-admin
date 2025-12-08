<?php

namespace App\Services;

use App\Models\IndicatorDaily;
use App\Models\IndicatorMonthly;
use Carbon\Carbon;

class IndicatorService
{
    public function getDailyIndicator(int $customerId, string $company, string $indicatorCode)
    {
        return IndicatorDaily::where('customer_id', $customerId)
            ->where('company', $company)
            ->where('indicator', $indicatorCode)
            ->orderBy('date', 'desc')
            ->first();
    }

    public function getLastUpdatedAtForImage(int $customerId)
    {
        return Carbon::parse(IndicatorDaily::where('customer_id', $customerId)->max('date'));
    }

    public function getMonthlyDelinquencyData(int $customerId, string $company)
    {
        $latestDate = IndicatorMonthly::where('customer_id', $customerId)
            ->where('company', $company)
            ->max('date');

        if (! $latestDate) {
            return collect();
        }

        $inadimplencia = IndicatorMonthly::where('customer_id', $customerId)
            ->where('company', $company)
            ->where('indicator', 'INADIMPLENCIA')
            ->orderBy('date', 'desc')
            ->limit(12)
            ->get()
            ->keyBy(fn ($item) => sprintf('%04d-%02d', $item->year, $item->month));

        $crVencido = IndicatorMonthly::where('customer_id', $customerId)
            ->where('company', $company)
            ->where('indicator', 'CR_VENCIDO')
            ->orderBy('date', 'desc')
            ->limit(12)
            ->get()
            ->keyBy(fn ($item) => sprintf('%04d-%02d', $item->year, $item->month));

        $allKeys = $inadimplencia->keys()->merge($crVencido->keys())->unique()->sort();

        return $allKeys->map(function ($key) use ($inadimplencia, $crVencido) {
            [$year, $month] = explode('-', $key);

            return (object) [
                'year' => (int) $year,
                'month' => (int) $month,
                'inadimplencia_actual' => $inadimplencia->get($key)?->actual ?? null,
                'inadimplencia_target' => $inadimplencia->get($key)?->target ?? null,
                'cr_vencido' => $crVencido->get($key)?->actual ?? null,
                'sort_key' => $key,
            ];
        })->values();
    }

    public function getMonthlySalesData(int $customerId, string $company)
    {
        $currentYear = IndicatorMonthly::where('customer_id', $customerId)
            ->where('company', $company)
            ->where('indicator', 'FATURAMENTO')
            ->max('year');

        if (! $currentYear) {
            return collect();
        }

        $previousYear = $currentYear - 1;

        return IndicatorMonthly::where('customer_id', $customerId)
            ->where('company', $company)
            ->where('indicator', 'FATURAMENTO')
            ->whereIn('year', [$previousYear, $currentYear])
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();
    }

    public function getTop10ReceivablesData(int $customerId, string $company)
    {
        $vencidos = IndicatorMonthly::where('customer_id', $customerId)
            ->where('company', $company)
            ->where('indicator', 'TOP_10_VENCIDOS')
            ->orderBy('month', 'asc')
            ->get();

        $aVencer = IndicatorMonthly::where('customer_id', $customerId)
            ->where('company', $company)
            ->where('indicator', 'TOP_10_A_VENCER')
            ->orderBy('month', 'asc')
            ->get();

        if ($vencidos->isEmpty() && $aVencer->isEmpty()) {
            return null;
        }

        return (object) [
            'vencidos' => $vencidos,
            'a_vencer' => $aVencer,
        ];
    }

    public function getMonthlyBalanceData(int $customerId, string $company)
    {
        return IndicatorMonthly::where('customer_id', $customerId)
            ->where('company', $company)
            ->where('indicator', 'SALDOS')
            ->orderBy('date', 'desc')
            ->limit(12)
            ->get()
            ->sortBy(fn ($item) => sprintf('%04d-%02d', $item->year, $item->month))
            ->values();
    }

    public function getIndicatorsForImage(array $indicators, int $customerId, string $company)
    {
        $result = [];

        foreach ($indicators as $indicator) {
            $data = $this->getDailyIndicator($customerId, $company, $indicator['code']);
            if ($data) {
                $result[] = [
                    'code' => $indicator['code'],
                    'position' => $indicator['position'],
                    'data' => $data,
                ];
            }
        }

        return $result;
    }

    public function getChartsForImage(array $charts, int $customerId, string $company)
    {
        $result = [];

        foreach ($charts as $chart) {
            if ($chart['type'] === 'delinquency') {
                $data = $this->getMonthlyDelinquencyData($customerId, $company);
                if ($data->isNotEmpty()) {
                    $result[] = [
                        'type' => $chart['type'],
                        'row' => $chart['row'],
                        'col' => $chart['col'],
                        'data' => $data,
                    ];
                }
            } elseif ($chart['type'] === 'monthly_sales') {
                $data = $this->getMonthlySalesData($customerId, $company);
                if ($data->isNotEmpty()) {
                    $result[] = [
                        'type' => $chart['type'],
                        'row' => $chart['row'],
                        'col' => $chart['col'],
                        'data' => $data,
                    ];
                }
            } elseif ($chart['type'] === 'top_10_receivables') {
                $data = $this->getTop10ReceivablesData($customerId, $company);
                if ($data !== null) {
                    $result[] = [
                        'type' => $chart['type'],
                        'row' => $chart['row'],
                        'col' => $chart['col'],
                        'data' => $data,
                    ];
                }
            } elseif ($chart['type'] === 'balance') {
                $data = $this->getMonthlyBalanceData($customerId, $company);
                if ($data->isNotEmpty()) {
                    $result[] = [
                        'type' => $chart['type'],
                        'row' => $chart['row'],
                        'col' => $chart['col'],
                        'data' => $data,
                    ];
                }
            }
        }

        return $result;
    }
}
