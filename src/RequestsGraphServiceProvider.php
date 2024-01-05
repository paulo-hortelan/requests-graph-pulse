<?php

namespace PauloHortelan\RequestsGraphPulse;

use Illuminate\Contracts\Foundation\Application;
use Livewire\LivewireManager;
use PauloHortelan\RequestsGraphPulse\Livewire\RequestsGraph;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class RequestsGraphServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('requests-graph-pulse')
            ->hasViews();
    }

    public function boot(): void
    {
        parent::boot();

        $this->callAfterResolving('livewire', function (LivewireManager $livewire, Application $app) {
            $livewire->component('requests-graph', RequestsGraph::class);
        });
    }
}
