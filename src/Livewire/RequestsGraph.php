<?php

namespace PauloHortelan\RequestsGraphPulse\Livewire;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Laravel\Pulse\Facades\Pulse;
use Laravel\Pulse\Livewire\Card;
use Laravel\Pulse\Livewire\Concerns;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Url;
use Livewire\Livewire;
use PauloHortelan\RequestsGraphPulse\Recorders\RequestsGraphRecorder;

#[Lazy]
class RequestsGraph extends Card
{
    use Concerns\HasPeriod, Concerns\RemembersQueries;

    private ?array $statusClasses = [
        'informational',
        'successful',
        'redirection',
        'client_error',
        'server_error',
    ];

    #[Url(as: 'requests-graph')]
    public function render()
    {

        [$requests, $time, $runAt] = $this->remember(fn () => Pulse::graph(
            $this->statusClasses,
            'count',
            $this->periodAsInterval(),
        ));

        if (Livewire::isLivewireRequest()) {
            $this->dispatch('requests-chart-update', request: $requests['request']);
        }

        return View::make('livewire.requests-graph', [
            'request' => $requests['request'],
            'time' => $time,
            'runAt' => $runAt,
            'config' => [
                'sample_rate' => Config::get('pulse.recorders.'.RequestsGraphRecorder::class.'.sample_rate'),
                'record_informational' => Config::get('pulse.recorders.'.RequestsGraphRecorder::class.'.record_informational'),
                'record_successful' => Config::get('pulse.recorders.'.RequestsGraphRecorder::class.'.record_successful'),
                'record_redirection' => Config::get('pulse.recorders.'.RequestsGraphRecorder::class.'.record_redirection'),
                'record_client_error' => Config::get('pulse.recorders.'.RequestsGraphRecorder::class.'.record_client_error'),
                'record_server_error' => Config::get('pulse.recorders.'.RequestsGraphRecorder::class.'.record_server_error'),
            ],
        ]);
    }
}
