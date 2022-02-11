<?php
namespace EscolaLms\Invoices\Services\Contracts;

use EscolaLms\Cart\Models\Order;
use LaravelDaily\Invoices\Invoice;

interface InvoicesServiceContract
{
    public function sandInvoices(Order $order): void;

    public function saveInvoices(Order $order): void;

    public function getInvoices(Order $order): Invoice;
}
