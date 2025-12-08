@php
    $months = ['JAN', 'FEV', 'MAR', 'ABR', 'MAI', 'JUN', 'JUL', 'AGO', 'SET', 'OUT', 'NOV', 'DEZ'];
    $labels = $data->map(fn($item) => $months[$item->month - 1] . '/' . substr($item->year, -2))->toArray();
    $saldosValues = $data->pluck('actual')->toArray();
    $chartId = 'saldosChart_' . uniqid();
@endphp

<div class="chart-container">
    <div class="chart-header">
        <h3 class="chart-title">SALDOS (ULT. 12 MESES)</h3>
    </div>

    <div class="chart-content">
        <canvas id="{{ $chartId }}"></canvas>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('{{ $chartId }}').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: 'Saldos',
                    data: @json($saldosValues),
                    backgroundColor: '#10B981',
                    borderColor: '#059669',
                    borderWidth: 1,
                    borderRadius: 4,
                    datalabels: {
                        anchor: 'start',
                        align: 'end',
                        rotation: -90,
                        formatter: function(value) {
                            if (value === 0 || value === null) return '';
                            return formatAbbreviatedNumber(value);
                        },
                        color: '#ffffff',
                        font: {
                            family: 'Inter',
                            size: 11,
                            weight: 'bold'
                        }
                    }
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        top: 40,
                        bottom: 10,
                        left: 10,
                        right: 10
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    datalabels: {
                        display: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            display: false
                        },
                        grid: {
                            display: true,
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                family: 'Inter',
                                size: 11,
                                weight: 'bold'
                            },
                            color: '#ffffff'
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

        function formatAbbreviatedNumber(number) {
            const negative = number < 0;
            const abs = Math.abs(number);

            let formatted;
            if (abs < 1000) {
                formatted = Math.floor(abs).toLocaleString('pt-BR');
            } else if (abs < 10000) {
                formatted = (abs / 1000).toFixed(2).replace('.', ',') + 'K';
                formatted = formatted.replace(/,?0+K$/, 'K');
            } else if (abs < 100000) {
                formatted = (abs / 1000).toFixed(1).replace('.', ',') + 'K';
                formatted = formatted.replace(/,?0+K$/, 'K');
            } else if (abs < 1000000) {
                formatted = Math.floor(abs / 1000).toLocaleString('pt-BR') + 'K';
            } else {
                formatted = (abs / 1000000).toFixed(2).replace('.', ',') + 'M';
                formatted = formatted.replace(/,?0+M$/, 'M');
            }

            return negative ? '-' + formatted : formatted;
        }
    });
</script>

<style>
    .chart-container {
        @apply bg-white rounded-lg shadow-md overflow-hidden;
    }

    .chart-header {
        @apply bg-gray-900 text-white px-4 py-3;
    }

    .chart-title {
        @apply text-sm font-bold uppercase tracking-wide;
    }

    .chart-content {
        @apply relative p-4;
        min-height: 300px;
    }
</style>
