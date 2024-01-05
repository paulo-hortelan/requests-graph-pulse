<?php

namespace PauloHortelan\RequestsGraphPulse\Recorders\Concern;

trait RequestResponse
{
    /**
     * Determine if the response should be recorded.
     */
    protected function shouldRecord(string $statusClass): bool
    {
        return $this->config->get('pulse.recorders.'.static::class.'.record_'.$statusClass) ?? false;
    }

    /**
     * Get the HTTP status class accordingly to the given code.
     */
    protected function getStatusClass(int $code): string
    {
        switch ($code) {
            case $code >= 0 && $code < 200:
                $statusClass = 'informational';
                break;
            case $code >= 200 && $code < 300:
                $statusClass = 'successful';
                break;
            case $code >= 300 && $code < 400:
                $statusClass = 'redirection';
                break;
            case $code >= 400 && $code < 500:
                $statusClass = 'client_error';
                break;
            case $code >= 500 && $code < 600:
                $statusClass = 'server_error';
                break;
            default:
                $statusClass = 'server_error';
                break;
        }

        return $statusClass;
    }
}
