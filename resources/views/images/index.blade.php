@php
    if (!function_exists('formatAbbreviatedNumber')) {
        function formatAbbreviatedNumber($number)
        {
            $negative = $number < 0;
            $abs = abs($number);

            if ($abs < 1000) {
                $formatted = number_format(floor($abs), 0, ',', '.');
            } elseif ($abs < 10000) {
                $formatted = number_format($abs / 1000, 2, ',', '.');
                $formatted = rtrim(rtrim($formatted, '0'), ',');
                $formatted .= 'K';
            } elseif ($abs < 100000) {
                $formatted = number_format($abs / 1000, 1, ',', '.');
                $formatted = rtrim(rtrim($formatted, '0'), ',');
                $formatted .= 'K';
            } elseif ($abs < 1000000) {
                $formatted = number_format(floor($abs / 1000), 0, '', '.');
                $formatted .= 'K';
            } else {
                $formatted = number_format($abs / 1000000, 2, ',', '.');
                $formatted = rtrim(rtrim($formatted, '0'), ',');
                $formatted .= 'M';
            }

            return $negative ? '-' . $formatted : $formatted;
        }
    }

    if (!function_exists('formatPercentage')) {
        function formatPercentage($number, $decimals = 2)
        {
            $negative = $number < 0;
            $abs = abs($number);

            if ($abs >= 100) {
                $formatted = number_format($abs, $decimals, ',', '.') . '%';
            } elseif ($abs >= 10) {
                $formatted = number_format($abs, $decimals, ',', '.') . '%';
            } else {
                $formatted = number_format($abs, $decimals, ',', '.') . '%';
            }

            return $negative ? '-' . $formatted : $formatted;
        }
    }
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Indicadores Consys</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Roboto+Mono:wght@400;700&display=swap"
        rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

    {{-- @vite(['resources/css/image.css']) --}}
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <script>
        // Função global para formatar números de forma abreviada
        function formatAbbreviatedNumber(number) {
            const negative = number < 0;
            const abs = Math.abs(number);
            let formatted;

            if (abs < 1000) {
                formatted = Math.floor(abs).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            } else if (abs < 10000) {
                formatted = (abs / 1000).toFixed(2).replace('.', ',');
                formatted = formatted.replace(/0+$/, '').replace(/,$/, '');
                formatted += 'K';
            } else if (abs < 100000) {
                formatted = (abs / 1000).toFixed(1).replace('.', ',');
                formatted = formatted.replace(/0+$/, '').replace(/,$/, '');
                formatted += 'K';
            } else if (abs < 1000000) {
                formatted = Math.floor(abs / 1000).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                formatted += 'K';
            } else {
                formatted = (abs / 1000000).toFixed(2).replace('.', ',');
                formatted = formatted.replace(/0+$/, '').replace(/,$/, '');
                formatted += 'M';
            }

            return negative ? '-' + formatted : formatted;
        }
    </script>
</head>

<body class="font-sans antialiased bg-white" style="font-family: 'Inter', sans-serif;">
    <div class="flex flex-col m-4">
        <div class="flex justify-between items-center flex-shrink-0 bg-gray-900 text-gray-100 p-4 mb-4">
            <div class="text-lg font-semibold">
                <img src="{{ asset('images/ConsysLogo.png') }}" class="h-6 mb-1 inline-block mr-2">
                {{ $customer->name }} - {{ $image->company }} | {{ $image->name }}
            </div>
            <div class="text-lg font-semibold uppercase">
                {{ \Illuminate\Support\Carbon::parse($lastUpdatedAt)->locale('pt_BR')->isoFormat('dddd, DD/MM/YYYY HH:mm') }}
            </div>
        </div>

        <div class="flex-1 overflow-hidden flex flex-col mb-4">
            <div class="flex-1 overflow-auto space-y-4">
                @foreach ($grid as $row)
                    <div class="grid grid-cols-4 gap-4">
                        @foreach ($row as $cell)
                            @if ($cell['type'] === 'indicator' && isset($cell['data']))
                                <div>
                                    @include('images.components.indicator', ['data' => $cell['data']])
                                </div>
                            @elseif ($cell['type'] === 'chart' && isset($cell['data']))
                                <div class="col-span-2">
                                    @if ($cell['chart_type'] === 'monthly_sales')
                                        @include('images.charts.monthly-sales', ['data' => $cell['data']])
                                    @elseif ($cell['chart_type'] === 'delinquency')
                                        @include('images.charts.delinquency', ['data' => $cell['data']])
                                    @elseif ($cell['chart_type'] === 'top_10_receivables')
                                        @include('images.charts.top-10-receivables', [
                                            'data' => $cell['data'],
                                        ])
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

        {{-- <div class="flex justify-center items-center bg-gray-900 text-gray-100 p-4">
            <span class="text-xs uppercase">Imagem gerada em:
                {{ \Illuminate\Support\Carbon::now()->locale('pt_BR')->isoFormat('dddd, DD/MM/YYYY HH:mm') }}</span>
        </div> --}}
    </div>
</body>

</html>
