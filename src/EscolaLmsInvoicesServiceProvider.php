<?php

namespace EscolaLms\Invoices;

use EscolaLms\Invoices\Services\Contracts\InvoicesServiceContract;
use EscolaLms\Invoices\Services\InvoicesService;
use Illuminate\Support\ServiceProvider;

/**
 * SWAGGER_VERSION
 */
class EscolaLmsInvoicesServiceProvider extends ServiceProvider
{
    public $bindings = [
        InvoicesServiceContract::class => InvoicesService::class,
    ];

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    public function register()
    {
        parent::register();

        $this->mergeConfigFrom(__DIR__ . '/../config/invoices.php', 'invoices');
    }

    protected function bootForConsole(): void
    {
        $this->publishes([
            __DIR__ . '/../config/invoices.php' => config_path('invoices.php'),
        ], 'escolalms_invoices.config');
    }
}
