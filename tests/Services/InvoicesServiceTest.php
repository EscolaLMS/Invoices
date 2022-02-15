<?php

namespace EscolaLms\Invoices\Tests\Services;

use EscolaLms\Cart\Models\Order;
use EscolaLms\Cart\Models\OrderItem;
use EscolaLms\Core\Models\User;
use EscolaLms\Core\Tests\CreatesUsers;
use EscolaLms\Invoices\Services\Contracts\InvoicesServiceContract;
use EscolaLms\Invoices\Tests\Models\Course;
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
        $courses = [
            ...Course::factory()->count(5)->create(),
            ...Course::factory()->count(5)->create(),
        ];
        $this->order = Order::factory()->for($this->user)->create();
        foreach ($courses as $course) {
            $orderItem = new OrderItem();
            $orderItem->buyable()->associate($course);
            $orderItem->quantity = 1;
            $orderItem->order_id = $this->order->getKey();
            $orderItem->save();
        }
    }

    public function testSaveInvoices(): void
    {
        $response = $this->service->saveInvoice($this->order);

        $this->assertFileExists(storage_path('app/public').'/'.$response);
    }
}
