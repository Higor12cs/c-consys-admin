@php
    $months = ['JAN', 'FEV', 'MAR', 'ABR', 'MAI', 'JUN', 'JUL', 'AGO', 'SET', 'OUT', 'NOV', 'DEZ'];
    $labels = $data->map(fn($item) => $months[$item->month - 1] . '/' . substr($item->year, -2))->toArray();
    $inadimplenciaValues = $data->pluck('inadimplencia_actual')->toArray();
    $crVencidoValues = $data->pluck('cr_vencido')->toArray();
    $chartId = 'delinquencyChart_' . uniqid();
@endphp

<div class="chart-container">
    <div class="chart-header">
        <h3 class="chart-title">INADIMPLÊNCIA (ULT. 12 MESES)</h3>
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
                    label: 'CR Vencido',
                    data: @json($crVencidoValues),
                    backgroundColor: '#EF4444',
                    borderColor: '#DC2626',
                    borderWidth: 1,
                    borderRadius: 4,
                    yAxisID: 'y1',
                    order: 2,
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
                }, {
                    label: 'Inadimplência (%)',
                    data: @json($inadimplenciaValues),
                    type: 'line',
                    borderColor: '#FCD34D',
                    backgroundColor: 'rgba(252, 211, 77, 0.1)',
                    borderWidth: 3,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointBackgroundColor: '#FCD34D',
                    pointBorderColor: '#FCD34D',
                    pointBorderWidth: 2,
                    tension: 0.3,
                    yAxisID: 'y',
                    order: 1,
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        formatter: function(value) {
                            if (value === null || value === undefined) return '';
                            return Number(value).toFixed(1) + '%';
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
                    datalabels: {
                        display: true
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: false,
                        position: 'right',
                        suggestedMin: function(context) {
                            const min = Math.min(...context.chart.data.datasets[1].data.filter(v =>
                                v !== null));
                            return min * -10;
                        },
                        suggestedMax: function(context) {
                            const max = Math.max(...context.chart.data.datasets[1].data.filter(v =>
                                v !== null));
                            return max * -10;
                        },
                        grid: {
                            display: false
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: false,
                        position: 'left',
                        grid: {
                            display: false
                        }
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
