<x-pulse::card :cols="$cols" :rows="$rows" :class="$class">
    <x-pulse::card-header name="Requests" title="Time: {{ number_format($time) }}ms; Run at: {{ $runAt }};"
        details="past {{ $this->periodForHumans() }}">
        <x-slot:icon>
            <x-pulse::icons.arrows-left-right />
        </x-slot:icon>
        @if (!$request->isEmpty())
        <x-slot:actions>
            <div class="flex flex-wrap gap-4">
                @if ($config['record_informational'])
                <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400 font-medium">
                    <div class="h-0.5 w-3 rounded-full bg-[rgba(29,153,172,0.5)]"></div>
                    Informational
                </div>
                @endif
                @if ($config['record_successful'])
                <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400 font-medium">
                    <div class="h-0.5 w-3 rounded-full bg-[#9333ea]"></div>
                    Successful
                </div>
                @endif
                @if ($config['record_redirection'])
                <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400 font-medium">
                    <div class="h-0.5 w-3 rounded-full bg-[rgba(107,114,128,0.5)]"></div>
                    Redirection
                </div>
                @endif
                @if ($config['record_client_error'])
                <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400 font-medium">
                    <div class="h-0.5 w-3 rounded-full bg-[#eab308]"></div>
                    Client Error
                </div>
                @endif
                @if ($config['record_server_error'])
                <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400 font-medium">
                    <div class="h-0.5 w-3 rounded-full bg-[#e11d48]"></div>
                    Server Error
                </div>
                @endif
            </div>
        </x-slot:actions>
        @endif
    </x-pulse::card-header>

    <x-pulse::scroll :expand="$expand" wire:poll.5s="">
        @if ($request->isEmpty())
        <x-pulse::no-results />
        @else
        <div class="grid gap-3 mx-px mb-px">
            <div wire:key="requests-graph">

                @php
                if(!function_exists('hightestValue')){
                    function hightestValue($data){
                        $highest = 0;
    
                        foreach($data as $item) {
                            $max = max($item);
                            $highest = $max > $highest ? $max : $highest;
                        }
    
                        return $highest;
                    }
                }
                

                $highest = hightestValue($request->toArray());
                @endphp

                <div class="mt-3 relative">
                    <div
                        class="absolute -left-px -top-2 max-w-fit h-4 flex items-center px-1 text-xs leading-none text-white font-bold bg-purple-500 rounded after:[--triangle-size:4px] after:border-l-purple-500 after:absolute after:right-[calc(-1*var(--triangle-size))] after:top-[calc(50%-var(--triangle-size))] after:border-t-[length:var(--triangle-size)] after:border-b-[length:var(--triangle-size)] after:border-l-[length:var(--triangle-size)] after:border-transparent">
                        @if ($config['sample_rate'] < 1) <span
                            title="Sample rate: {{ $config['sample_rate'] }}, Raw value: {{ number_format($highest) }}">
                            ~{{ number_format($highest * (1 / $config['sample_rate'])) }}</span>
                            @else
                            {{ number_format($highest) }}
                            @endif
                    </div>

                    <div wire:ignore class="" x-data="requestsChart({
                                    request: @js($request),
                                    sampleRate: {{ $config['sample_rate'] }}
                                })">
                        <canvas x-ref="canvas"
                            class="h-52 ring-1 ring-gray-900/5 dark:ring-gray-100/10 bg-gray-50 dark:bg-gray-800 rounded-md shadow-sm"></canvas>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </x-pulse::scroll>
</x-pulse::card>

@script
<script>
    Alpine.data('requestsChart', (data) => ({

    init() {
        var request = data.request;

        let chart = new Chart(
            this.$refs.canvas,
            {
                type: 'line',
                data: {
                    labels: this.labels(request[Object.keys(request)[0]]),
                    datasets: [
                        {
                            label: 'Server Error',
                            borderColor: '#e11d48',
                            borderWidth: 2,
                            borderCapStyle: 'round',
                            data: this.scale(request.server_error),
                            pointHitRadius: 10,
                            pointStyle: false,
                            tension: 0.2,
                            spanGaps: false,
                        },  
                        {
                            label: 'Client Error',
                            borderColor: '#eab308',
                            borderWidth: 2,
                            borderCapStyle: 'round',
                            data: this.scale(request.client_error),
                            pointHitRadius: 10,
                            pointStyle: false,
                            tension: 0.2,
                            spanGaps: false,
                        },  
                        {
                            label: 'Successful',
                            borderColor: '#9333ea',
                            borderWidth: 2,
                            borderCapStyle: 'round',
                            data: this.scale(request.successful),
                            pointHitRadius: 10,
                            pointStyle: false,
                            tension: 0.2,
                            spanGaps: false,
                        },
                        {
                            label: 'Informational',
                            borderColor: 'rgba(29,153,172,0.5)',
                            borderWidth: 2,
                            borderCapStyle: 'round',
                            data: this.scale(request.informational),
                            pointHitRadius: 10,
                            pointStyle: false,
                            tension: 0.2,
                            spanGaps: false,
                        },                        
                        {
                            label: 'Redirection',
                            borderColor: 'rgba(107,114,128,0.5)',
                            borderWidth: 2,
                            borderCapStyle: 'round',
                            data: this.scale(request.redirection),
                            pointHitRadius: 10,
                            pointStyle: false,
                            tension: 0.2,
                            spanGaps: false,
                        },                        
                    ],
                },
                options: {
                    maintainAspectRatio: false,
                    layout: {
                        autoPadding: false,
                        padding: {
                            top: 3,
                        },
                    },
                    datasets: {
                        line: {
                            borderWidth: 2,
                            borderCapStyle: 'round',
                            pointHitRadius: 10,
                            pointStyle: false,
                            tension: 0.2,
                            spanGaps: false,
                            segment: {
                                borderColor: (ctx) => ctx.p0.raw === 0 && ctx.p1.raw === 0 ? 'transparent' : undefined,
                            }
                        }
                    },                    
                    scales: {
                        x: {
                            display: false,
                        },
                        y: {
                            display: false,
                            min: 0,
                            max: this.highest(request),
                        },
                    },
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            mode: 'index',
                            position: 'nearest',
                            intersect: false,
                            callbacks: {
                                beforeBody: (context) => context
                                    .filter(item => item.raw > 0)
                                    .map(item => `${item.dataset.label}: ${data.sampleRate < 1 ? '~' : ''}${item.formattedValue}`)
                                    .join(', '),
                                label: () => null,
                            },
                        },
                    },
                },
            }
        )

        Livewire.on('requests-chart-update', ({ request }) => {

            if (chart === undefined) {
                return
            }

            if (request === undefined && chart) {
                chart.destroy()
                chart = undefined
                return
            }

            chart.data.labels = this.labels(request[Object.keys(request)[0]])
            chart.options.scales.y.max = this.highest(request)
            chart.data.datasets[0].data = this.scale(request.server_error)
            chart.data.datasets[1].data = this.scale(request.client_error)
            chart.data.datasets[2].data = this.scale(request.successful)
            chart.data.datasets[3].data = this.scale(request.informational)
            chart.data.datasets[4].data = this.scale(request.redirection)
            chart.update()
        })
    },
    labels(status) {
        return Object.keys(status)
    },    
    scale(status) {
        return Object.values(status).map(value => value * (1 / data.sampleRate ))
    },    
    highest(request) {
        var highest = 0;

        Object.keys(request).map(status => {
            let max = Math.max(...Object.values(request[status]))
            highest = max > highest ? max : highest
        })

        return highest
    }    
}))
</script>
@endscript
