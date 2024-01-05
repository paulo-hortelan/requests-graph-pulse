<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Laravel\Pulse\Facades\Pulse;
use PauloHortelan\RequestsGraphPulse\Recorders\RequestsGraphRecorder;

use function Pest\Laravel\get;

it('captures informational requests', function () {
    Date::setTestNow('2000-01-02 03:04:05');

    Route::get('test-route-informational', function () {
        return response('', 100);
    });

    Config::set('pulse.recorders.'.RequestsGraphRecorder::class.'.record_informational', true);
    get('test-route-informational')->assertStatus(100);

    Pulse::ignore(fn () => expect(DB::table('pulse_aggregates')->where('type','informational')->get())->toHaveCount(4));
    Pulse::ignore(fn () => expect(DB::table('pulse_entries')->count())->toBe(0));
    Pulse::ignore(fn () => expect(DB::table('pulse_values')->count())->toBe(0));    
});

it('captures successful requests', function () {
    Date::setTestNow('2000-01-02 03:04:05');

    Route::get('test-route-successful', function () {
        return response('', 200);
    });

    Config::set('pulse.recorders.'.RequestsGraphRecorder::class.'.record_successful', true);
    get('test-route-successful')->assertStatus(200);

    Pulse::ignore(fn () => expect(DB::table('pulse_aggregates')->where('type','successful')->get())->toHaveCount(4));
    Pulse::ignore(fn () => expect(DB::table('pulse_entries')->count())->toBe(0));
    Pulse::ignore(fn () => expect(DB::table('pulse_values')->count())->toBe(0));    
});

it('does not capture \'requests\' requests', function () {
    Config::set('pulse.recorders.'.RequestsGraphRecorder::class.'.record_successful', false);
    Date::setTestNow('2000-01-02 03:04:05');

    Route::get('test-route', function () {
        return response('', 200);
    });

    get('test-route');

    Pulse::ignore(fn () => expect(DB::table('pulse_entries')->get())->toHaveCount(0));
    Pulse::ignore(fn () => expect(DB::table('pulse_aggregates')->get())->toHaveCount(0));
    Pulse::ignore(fn () => expect(DB::table('pulse_values')->count())->toBe(0));
});