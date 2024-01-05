<?php

namespace PauloHortelan\RequestsGraphPulse\Recorders;

use Carbon\CarbonImmutable;
use Laravel\Pulse\Pulse;
use Illuminate\Config\Repository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Pulse\Concerns\ConfiguresAfterResolving;
use Laravel\Pulse\Recorders\Concerns;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Route;
use PauloHortelan\RequestsGraphPulse\Recorders\Concern\RequestResponse;

class RequestsGraphRecorder
{
    use Concerns\Ignores,
        Concerns\LivewireRoutes,
        Concerns\Sampling,
        Concerns\Thresholds,
        RequestResponse,
        ConfiguresAfterResolving;

    public function __construct(
        protected Pulse $pulse,
        protected Repository $config
    ) {
        //
    }

    /**
     * Register the recorder.
     */
    public function register(callable $record, Application $app): void
    {
        $this->afterResolving(
            $app,
            Kernel::class,
            fn (Kernel $kernel) => $kernel->whenRequestLifecycleIsLongerThan(-1, $record) // @phpstan-ignore method.notFound
        );
    }

    public function record(Carbon $startedAt, Request $request, Response $response): void
    {
        if (!$request->route() instanceof Route || !$this->shouldSample() || $this->shouldIgnore($this->resolveRoutePath($request)[0])) {
            return;
        }

        $statusCode = $response->getStatusCode();
        $statusClass = $this->getStatusClass($statusCode);

        if (!$this->shouldRecord($statusClass)) {
            return;
        }

        $this->pulse->lazy(function () use ($statusClass) {
            $this->pulse->record(
                type: $statusClass,
                key: "request",
                timestamp: CarbonImmutable::now()->getTimestamp(),
            )->count()->onlyBuckets();
        });
    }
}
