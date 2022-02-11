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

        $this->mergeConfigFrom(__DIR__.'/config/invoices.php', 'invoices');

        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    public function register()
    {
        parent::register();
    }

    protected function bootForConsole(): void
    {
    }
}
