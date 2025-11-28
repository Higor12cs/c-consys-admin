@php
    if ($data->direction === 0) {
        $bgColor = 'bg-gray-400';
    } else {
        $threshold = abs($data->target * 0.05);

        if ($data->direction === 1) {
            $diff = $data->actual - $data->target;
            if ($diff >= 0) {
                $bgColor = 'bg-green-700';
            } elseif (abs($diff) <= $threshold) {
                $bgColor = 'bg-amber-500';
            } else {
                $bgColor = 'bg-red-700';
            }
        } else {
            $diff = $data->target - $data->actual;
            if ($diff >= 0) {
                $bgColor = 'bg-green-700';
            } elseif (abs($diff) <= $threshold) {
                $bgColor = 'bg-amber-500';
            } else {
                $bgColor = 'bg-red-700';
            }
        }
    }

    $displayDiff = abs($data->actual - $data->target);
@endphp

@php
    try {
        $indicatorMap = \Illuminate\Support\Facades\Cache::remember('indicators_map', 60 * 5, function () {
            return \App\Models\Indicator::all()
                ->keyBy('code')
                ->map(function ($i) {
                    return ['description' => $i->description, 'is_percentage' => (bool) $i->is_percentage];
                })
                ->toArray();
        });
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::error('Failed to load indicators_map cache', ['error' => $e->getMessage()]);
        \App\Models\Notification::create([
            'type' => 'error',
            'title' => 'Erro de Cache',
            'message' => 'Erro ao carregar mapa de indicadores.',
            'context' => $e->getMessage(),
        ]);
        $indicatorMap = [];
    }

    $indicatorCode = str_pad($data->indicator, 4, '0', STR_PAD_LEFT);

    if (!empty($indicatorMap) && array_key_exists($indicatorCode, $indicatorMap)) {
        $indicatorDescription = $indicatorMap[$indicatorCode]['description'] ?? $data->indicator;
        $isPercentage = !empty($indicatorMap[$indicatorCode]['is_percentage']);
    } else {
        $indicatorDescription = $data->indicator;
        $isPercentage = false;
    }
@endphp

<div class="{{ $bgColor }} text-white shadow p-4 h-full flex flex-col">
    {{-- Nome --}}
    <p class="flex items-center gap-2 mb-3">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-chart-area-icon lucide-chart-area">
            <path d="M3 3v16a2 2 0 0 0 2 2h16" />
            <path
                d="M7 11.207a.5.5 0 0 1 .146-.353l2-2a.5.5 0 0 1 .708 0l3.292 3.292a.5.5 0 0 0 .708 0l4.292-4.292a.5.5 0 0 1 .854.353V16a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1z" />
        </svg>
        <span class="font-semibold uppercase truncate">
            {{ $indicatorDescription }}
        </span>
    </p>

    {{-- Dados --}}
    <div class="flex-1 flex flex-col justify-between">
        <div class="grid grid-cols-3 gap-2">

            {{-- Valor --}}
            <div class="flex justify-center items-center col-span-2">
                <p class="text-4xl font-semibold leading-tight mb-3">
                    @if ($isPercentage)
                        {{ formatPercentage($data->actual) }}
                    @else
                        {{ formatAbbreviatedNumber($data->actual) }}
                    @endif
                </p>
            </div>

            {{-- Meta e Diferença --}}
            <div class="space-y-2">
                <div>
                    <p class="text-xs opacity-85 mb-1">Meta</p>
                    <p class="font-semibold">
                        @if ($isPercentage)
                            {{ formatPercentage($data->target) }}
                        @else
                            {{ formatAbbreviatedNumber($data->target) }}
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs opacity-85 mb-1">Dif. R$</p>
                    <p class="font-semibold">
                        @if ($isPercentage)
                            {{ formatPercentage($displayDiff) }}
                        @else
                            {{ formatAbbreviatedNumber($displayDiff) }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
