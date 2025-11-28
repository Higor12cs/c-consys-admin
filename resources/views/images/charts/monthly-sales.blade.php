@php
    $months = ['JAN', 'FEV', 'MAR', 'ABR', 'MAI', 'JUN', 'JUL', 'AGO', 'SET', 'OUT', 'NOV', 'DEZ'];

    $currentYear = $data->max('year');
    $previousYear = $currentYear - 1;

    $previousYearData = $data->where('year', $previousYear);
    $currentYearData = $data->where('year', $currentYear);

    $labels = [];
    $previousYearActual = [];
    $currentYearActual = [];
    $currentYearTarget = [];

    foreach (range(1, 12) as $month) {
        $labels[] = $months[$month - 1];

        $prevItem = $previousYearData->firstWhere('month', $month);
        $previousYearActual[] = $prevItem ? $prevItem->actual : 0;

        $currItem = $currentYearData->firstWhere('month', $month);
        $currentYearActual[] = $currItem ? $currItem->actual : 0;
        $currentYearTarget[] = $currItem ? $currItem->target : null;
    }

    $chartId = 'monthlySalesChart_' . uniqid();
@endphp

<div class="chart-container">
    <div class="chart-header">
        <h3 class="chart-title">VENDAS MENSAIS</h3>
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
                    label: '{{ $previousYear }}',
                    data: @json($previousYearActual),
                    backgroundColor: '#3B82F6',
                    borderColor: '#2563EB',
                    borderWidth: 1,
                    borderRadius: 4,
                    order: 2,
                    datalabels: {
                        anchor: 'start',
                        align: 'end',
                        rotation: -90,
                        formatter: function(value) {
                            if (value === 0) return '';
                            return new Intl.NumberFormat('pt-BR', {
                                notation: 'compact',
                                compactDisplay: 'short'
                            }).format(value);
                        },
                        color: '#ffffff',
                        font: {
                            family: 'Inter',
                            size: 11,
                            weight: 'bold'
                        }
                    }
                }, {
                    label: '{{ $currentYear }}',
                    data: @json($currentYearActual),
                    backgroundColor: '#10B981',
                    borderColor: '#059669',
                    borderWidth: 1,
                    // borderRadius: 4,
                    order: 1,
                    datalabels: {
                        anchor: 'start',
                        align: 'end',
                        rotation: -90,
                        formatter: function(value) {
                            if (value === 0) return '';
                            return new Intl.NumberFormat('pt-BR', {
                                notation: 'compact',
                                compactDisplay: 'short'
                            }).format(value);
                        },
                        color: '#ffffff',
                        font: {
                            family: 'Inter',
                            size: 11,
                            weight: 'bold'
                        }
                    }
                }, {
                    label: 'Meta {{ $currentYear }}',
                    data: @json($currentYearTarget),
                    type: 'line',
                    borderColor: '#FCD34D',
                    backgroundColor: 'rgba(252, 211, 77, 0.1)',
                    borderWidth: 3,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointBackgroundColor: '#FCD34D',
                    pointBorderColor: '#FCD34D',
                    pointBorderWidth: 0,
                    tension: 0.3,
                    order: 0,
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        formatter: function(value) {
                            if (value === null || value === 0) return '';
                            return new Intl.NumberFormat('pt-BR', {
                                notation: 'compact',
                                compactDisplay: 'short'
                            }).format(value);
                        },
                        color: '#000000',
                        backgroundColor: '#FCD34D',
                        borderRadius: 4,
                        padding: 4,
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
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            color: '#ffffff',
                            font: {
                                family: 'Inter',
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('pt-BR', {
                                        style: 'currency',
                                        currency: 'BRL'
                                    }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    },
                    datalabels: {
                        display: true
                    }
                },
                scales: {
                    y: {
                        display: false
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#ffffff',
                            font: {
                                family: 'Inter',
                                size: 11,
                                weight: 'bold'
                            }
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
    });
</script>

<style>
    .chart-container {
        background: #000000;
        padding: 20px;
        border-radius: 0px;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .chart-header {
        margin-bottom: 20px;
        text-align: center;
    }

    .chart-title {
        font-size: 18px;
        font-weight: bold;
        color: #ffffff;
        margin: 0;
    }

    .chart-content {
        flex: 1;
        position: relative;
        min-height: 250px;
    }
</style>
