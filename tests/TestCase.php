<?php

namespace EscolaLms\Invoices\Tests;

use EscolaLms\Cart\CartServiceProvider;
use EscolaLms\Core\Models\User;
use EscolaLms\Invoices\EscolaLmsInvoicesServiceProvider;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\PassportServiceProvider;
use LaravelDaily\Invoices\InvoiceServiceProvider;
use Spatie\Permission\PermissionServiceProvider;

class TestCase extends \EscolaLms\Core\Tests\TestCase
{
    use DatabaseTransactions;

    protected function getPackageProviders($app): array
    {
        return [
            ...parent::getPackageProviders($app),
            PassportServiceProvider::class,
            PermissionServiceProvider::class,
            EscolaLmsInvoicesServiceProvider::class,
            CartServiceProvider::class,
            InvoiceServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('auth.providers.users.model', User::class);
        $app['config']->set('passport.client_uuids', true);
    }
}
