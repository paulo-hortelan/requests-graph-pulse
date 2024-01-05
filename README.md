<p align="center"><img src="/images/requests-graph.png" alt="Requests Graph for Laravel Pulse"></p>

# Requests Graph for Laravel Pulse

[![Latest Version on Packagist](https://img.shields.io/packagist/v/paulo-hortelan/requests-graph-pulse.svg?style=flat-square)](https://packagist.org/packages/paulo-hortelan/requests-graph-pulse)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/paulo-hortelan/requests-graph-pulse/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/paulo-hortelan/requests-graph-pulse/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/paulo-hortelan/requests-graph-pulse/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/paulo-hortelan/requests-graph-pulse/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/paulo-hortelan/requests-graph-pulse.svg?style=flat-square)](https://packagist.org/packages/paulo-hortelan/requests-graph-pulse)

Credits to [Aaron Francis](https://github.com/aarondfrancis) for his Pulse tutorial.

This is a Laravel Pulse package that adds a graph showing the latest requests. 

- Customizable requests status to be shown

## Installation

You can install the package via composer:

```bash
composer require paulo-hortelan/requests-graph-pulse
```

## Register the recorder

Add the `RequestsGraphRecorder` inside `config/pulse.php`. (If you don\'t have this file make sure you have published the config file of Larave Pulse using `php artisan vendor:publish --tag=pulse-config`)

```
return [
    // ...

    'recorders' => [
        // Existing recorders...

        \PauloHortelan\RequestsGraphPulse\Recorders\RequestsGraphRecorder::class => [
            'enabled' => env('PULSE_REQUESTS_GRAPH_ENABLED', true),
            'sample_rate' => env('PULSE_REQUESTS_GRAPH_SAMPLE_RATE', 1),
            'record_informational' => env('PULSE_REQUESTS_GRAPH_RECORD_INFORMATIONAL', false),
            'record_successful' => env('PULSE_REQUESTS_GRAPH_RECORD_SUCCESSFUL', true),
            'record_redirection' => env('PULSE_REQUESTS_GRAPH_RECORD_REDIRECTION', false),
            'record_client_error' => env('PULSE_REQUESTS_GRAPH_RECORD_CLIENT_ERROR', true),
            'record_server_error' => env('PULSE_REQUESTS_GRAPH_RECORD_SERVER_ERROR', true),
            'ignore' => [
                '#^/pulse$#', // Pulse dashboard...
            ],            
        ], 
    ]
]
```

## Add to your dashboard

To add the card to the Pulse dashboard, you must first [publish the vendor view](https://laravel.com/docs/10.x/pulse#dashboard-customization).

```bash
php artisan vendor:publish --tag=pulse-dashboard
```

Then, you can modify the `dashboard.blade.php` file and add the requests-graph livewire template:

```php
<livewire:requests-graph cols="6" />
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Paulo Hortelan](https://github.com/paulo-hortelan)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.