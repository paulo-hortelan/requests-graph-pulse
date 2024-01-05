<?php

use Illuminate\Support\Facades\Gate;
use PauloHortelan\RequestsGraphPulse\Tests\TestCase;

uses(TestCase::class)
    ->beforeEach(function () {
        Gate::define('viewPulse', fn ($user = null) => true);
    })
    ->in(__DIR__);
