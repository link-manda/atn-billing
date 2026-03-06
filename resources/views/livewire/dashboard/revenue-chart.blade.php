<div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden flex flex-col h-full">
    <div class="px-5 py-4 border-b border-zinc-100 dark:border-zinc-800">
        <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Revenue Trend</h3>
        <p class="text-xs text-zinc-500 mt-0.5">Paid invoices over the last 6 months</p>
    </div>

    <div class="p-5 flex-1">
        <div
            class="w-full"
            x-data="{
                initChart() {
                    let isDark = document.documentElement.classList.contains('dark');
                    let options = {
                        series: [{
                            name: 'Revenue',
                            data: @js($chartData)
                        }],
                        chart: {
                            type: 'area',
                            height: 280,
                            fontFamily: 'inherit',
                            background: 'transparent',
                            toolbar: { show: false },
                            zoom: { enabled: false }
                        },
                        colors: ['#2563EB'], // Blue-600
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shadeIntensity: 1,
                                opacityFrom: 0.45,
                                opacityTo: 0.05,
                                stops: [50, 100]
                            }
                        },
                        dataLabels: { enabled: false },
                        stroke: { curve: 'smooth', width: 3 },
                        xaxis: {
                            categories: @js($categories),
                            labels: { style: { colors: isDark ? '#a1a1aa' : '#71717a' } },
                            axisBorder: { show: false },
                            axisTicks: { show: false }
                        },
                        yaxis: {
                            labels: {
                                style: { colors: isDark ? '#a1a1aa' : '#71717a' },
                                formatter: function (val) {
                                    return 'Rp ' + (val / 1000).toFixed(0) + 'k';
                                }
                            }
                        },
                        grid: {
                            borderColor: isDark ? '#27272a' : '#f4f4f5',
                            strokeDashArray: 4,
                            xaxis: { lines: { show: true } },
                            yaxis: { lines: { show: true } },
                            padding: { top: 0, right: 0, bottom: 0, left: 10 }
                        },
                        theme: {
                            mode: isDark ? 'dark' : 'light'
                        },
                        tooltip: {
                            y: {
                                formatter: function (val) {
                                    return 'Rp ' + val.toLocaleString('id-ID');
                                }
                            }
                        }
                    };

                    let chart = new ApexCharts($refs.chart, options);
                    chart.render();

                    // Handle dark mode changes
                    const observer = new MutationObserver((mutations) => {
                        mutations.forEach((mutation) => {
                            if (mutation.attributeName === 'class') {
                                const newIsDark = document.documentElement.classList.contains('dark');
                                chart.updateOptions({
                                    theme: { mode: newIsDark ? 'dark' : 'light' },
                                    xaxis: { labels: { style: { colors: newIsDark ? '#a1a1aa' : '#71717a' } } },
                                    yaxis: { labels: { style: { colors: newIsDark ? '#a1a1aa' : '#71717a' } } },
                                    grid: { borderColor: newIsDark ? '#27272a' : '#f4f4f5' }
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
    </div>
</div>
