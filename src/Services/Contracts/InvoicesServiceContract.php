<?php
namespace EscolaLms\Invoices\Services\Contracts;

use EscolaLms\Cart\Models\Order;
use LaravelDaily\Invoices\Invoice;

interface InvoicesServiceContract
{
    public function saveInvoices(Order $order): string;

    public function getInvoices(Order $order): Invoice;
}
