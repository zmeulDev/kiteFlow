<!-- projects/visiflow/resources/views/livewire/dashboard/traffic-chart.blade.php -->
<div wire:init="loadChart" class="p-8 bg-white dark:bg-slate-900 rounded-[32px] border border-slate-200 dark:border-slate-800 shadow-sm transition-all">
    <header class="mb-8 flex items-center justify-between">
        <div>
            <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight uppercase italic">Hourly <span class="text-indigo-600">Traffic</span></h3>
            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-black uppercase tracking-widest mt-1">Visitor activity over last 12h</p>
        </div>
        <div class="h-10 w-10 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 rounded-2xl flex items-center justify-center text-lg border border-indigo-100 dark:border-indigo-500/20">📈</div>
    </header>

    @if(!$readyToLoad)
        <div class="h-64 animate-pulse bg-slate-50 rounded-2xl dark:bg-slate-800/50"></div>
    @else
        <div 
            wire:ignore
            x-data="{
                init() {
                    this.$nextTick(() => {
                        if (!this.$refs.chart) return;
                        
                        let chart = new ApexCharts(this.$refs.chart, {
                            chart: {
                                type: 'area',
                                height: 250,
                                toolbar: { show: false },
                                animations: { enabled: true },
                                fontFamily: 'Inter, sans-serif'
                            },
                            series: [{
                                name: 'Visitors',
                                data: @js($hourlyData)
                            }],
                            fill: {
                                type: 'gradient',
                                gradient: {
                                    shadeIntensity: 1,
                                    opacityFrom: 0.45,
                                    opacityTo: 0.05,
                                    stops: [20, 100, 100, 100]
                                }
                            },
                            dataLabels: { enabled: false },
                            stroke: { curve: 'smooth', width: 3, colors: ['#4f46e5'] },
                            xaxis: {
                                categories: ['-12h', '-10h', '-8h', '-6h', '-4h', '-2h', 'Now'],
                                axisBorder: { show: false },
                                axisTicks: { show: false }
                            },
                            yaxis: { show: false },
                            grid: { show: false },
                            colors: ['#4f46e5']
                        });
                        chart.render();
                    });
                }
            }"
        >
            <div x-ref="chart"></div>
        </div>
    @endif
</div>
