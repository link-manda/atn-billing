<div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden flex flex-col h-full">
    <div class="px-5 py-4 border-b border-zinc-100 dark:border-zinc-800">
        <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Invoice Status</h3>
        <p class="text-xs text-zinc-500 mt-0.5">Current year overview</p>
    </div>

    <div class="p-5 flex-1 flex items-center justify-center">
        @if(empty(array_filter($chartData)))
            <div class="text-center">
                <flux:icon.chart-pie class="w-10 h-10 mx-auto text-zinc-200 dark:text-zinc-700 mb-2" />
                <p class="text-sm text-zinc-400">No invoice data available</p>
            </div>
        @else
            <div
                class="w-full"
                x-data="{
                    initChart() {
                        let options = {
                            series: @js($chartData),
                            labels: @js($labels),
                            chart: {
                                type: 'donut',
                                height: 280,
                                fontFamily: 'inherit',
                                background: 'transparent',
                                toolbar: { show: false }
                            },
                            colors: ['#10B981', '#3B82F6', '#EF4444', '#71717A'],
                            dataLabels: { enabled: false },
                            stroke: { colors: 'transparent' },
                            legend: {
                                position: 'bottom',
                                labels: { colors: document.documentElement.classList.contains('dark') ? '#a1a1aa' : '#52525b' }
                            },
                            plotOptions: {
                                pie: { donut: { size: '65%' } }
                            },
                            theme: {
                                mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
                            }
                        };
                        let chart = new ApexCharts($refs.chart, options);
                        chart.render();

                        // Handle dark mode changes
                        const observer = new MutationObserver((mutations) => {
                            mutations.forEach((mutation) => {
                                if (mutation.attributeName === 'class') {
                                    const isDark = document.documentElement.classList.contains('dark');
                                    chart.updateOptions({
                                        theme: { mode: isDark ? 'dark' : 'light' },
                                        legend: { labels: { colors: isDark ? '#a1a1aa' : '#52525b' } }
                                    });
                                }
                            });
                        });
                        observer.observe(document.documentElement, { attributes: true });
                    }
                }"
                x-init="initChart()"
            >
                <div x-ref="chart"></div>
            </div>
        @endif
    </div>
</div>
