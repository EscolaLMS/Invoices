<?php

namespace EscolaLms\Invoices\Tests\Api;

use EscolaLms\Cart\Database\Seeders\CartPermissionSeeder;
use EscolaLms\Core\Models\User;
use EscolaLms\Cart\Models\Order;
use EscolaLms\Core\Tests\CreatesUsers;
use EscolaLms\Invoices\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class InvoicesApiTest extends TestCase
{
    use DatabaseTransactions;
    use CreatesUsers;

    private Order $order;
    private User $admin;
    private User $user;
    private User $user2;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(CartPermissionSeeder::class);
        $this->admin =  $this->makeAdmin();
        $this->user =  $this->makeStudent();
        $this->user2 =  $this->makeStudent();
        $this->order = Order::factory()->for($this->user)->create();;
    }

    public function testCanReadInvoices(): void
    {
        $response = $this->actingAs($this->user, 'api')->getJson('api/invoices/'.$this->order->getKey());

        $response->assertOk();
    }

    public function testCannotFindMissingOrder(): void
    {
        $response = $this->actingAs($this->user, 'api')->getJson('api/invoices/999999');

        $response->assertStatus(404);
    }

    public function testAdminCanReadInvoicesByOrderId(): void
    {
        $response = $this->actingAs($this->admin, 'api')->getJson('api/invoices/'.$this->order->getKey());

        $response->assertOk();
    }

    public function testOtherUsersCannotReadInvoicesOtherUser(): void
    {
        $response = $this->actingAs($this->user2, 'api')->getJson('api/invoices/'.$this->order->getKey());

        $response->assertForbidden();
    }

    public function testGuestCannotReadInvoices(): void
    {
        $response = $this->getJson('api/invoices/'.$this->order->getKey());

        $response->assertForbidden();
    }
}
