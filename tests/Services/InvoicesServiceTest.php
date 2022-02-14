<?php

namespace EscolaLms\Invoices\Tests\Services;

use EscolaLms\Cart\Models\Order;
use EscolaLms\Core\Models\User;
use EscolaLms\Core\Tests\CreatesUsers;
use EscolaLms\Invoices\Services\Contracts\InvoicesServiceContract;
use EscolaLms\Invoices\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class InvoicesServiceTest extends TestCase
{
    use DatabaseTransactions;
    use CreatesUsers;

    protected InvoicesServiceContract $service;

    private Order $order;
    private User $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = app(InvoicesServiceContract::class);
        $this->user =  $this->makeStudent();
        $this->order = Order::factory()->for($this->user)->create();
    }

    public function testSaveInvoices(): void
    {
        $this->service->saveInvoices($this->order);
    }

    public function testSendInvoices(): void
    {
        $this->service->sandInvoices($this->order);
    }
}
