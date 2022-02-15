<?php

namespace EscolaLms\Invoices\Services;

use EscolaLms\Cart\Models\Order;
use EscolaLms\Cart\Models\OrderItem;
use EscolaLms\Invoices\Services\Contracts\InvoicesServiceContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Config;
use LaravelDaily\Invoices\Facades\Invoice;
use LaravelDaily\Invoices\Invoice as InvoiceModel;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Classes\InvoiceItem;

class InvoicesService implements InvoicesServiceContract
{
    public function saveInvoice(Order $order): string
    {
        $invoice = $this->createInvoice($order);
        $invoice->save('public');

        return $invoice->filename;
    }

    public function createInvoice(Order $order): InvoiceModel
    {
        $customer = $this->prepareCustomer($order);
        $items = $this->prepareProducts($order->items);
        $notes = $this->prepareNote($order);

        $invoice = $this->setParamsFromConfig(Invoice::make());

        $invoice->status($order->status_name)
            ->buyer($customer)
            ->addItems($items)
            ->notes($notes)
            ->filename($this->filter_filename($customer->name.'_fv_'.$order->id));

        return $invoice;
    }

    private function setParamsFromConfig(InvoiceModel $invoice): InvoiceModel
    {
        $client = $this->prepareClient();

        $invoice->series(Config::get('invoices.serial_number.series', 'TEST'))
            ->sequence(Config::get('invoices.serial_number.sequence', 667))
            ->serialNumberFormat(Config::get('invoices.serial_number.format', '{SEQUENCE}/{SERIES}'))
            ->seller($client)
            ->dateFormat(Config::get('invoices.date.format', 'd-m-Y'))
            ->payUntilDays(Config::get('invoices.date.pay_until_days', 14))
            ->currencySymbol(Config::get('invoices.currency.symbol', '$'))
            ->currencyFraction(Config::get('invoices.currency.fraction', '$'))
            ->currencyCode(Config::get('invoices.currency.code', 'USD'))
            ->currencyDecimals(Config::get('invoices.currency.decimals', 2))
            ->currencyFormat(Config::get('invoices.currency.format', '{SYMBOL}{VALUE}'))
            ->currencyThousandsSeparator(Config::get('invoices.currency.decimal_point', '.'))
            ->currencyDecimalPoint(Config::get('invoices.currency.thousands_separator', ','))
            ->logo(public_path(Config::get('invoices.logo', 'vendor/invoices/sample-logo.png')));

        return $invoice;
    }

    private function prepareClient(): Party
    {
        return new Party(Config::get('invoices.seller', [
            'name' => 'Escola',
        ]));
    }

    private function prepareCustomer(Order $order): Party
    {
        return new Party([
            'name' => $order->first_name ?? $order->user->first_name . " " . $order->last_name ?? $order->user->last_name,
            'address' => $order->address ?? '',
            'code' => $order->post_code ?? '',
            'custom_fields' => [
                'order number' => $order->id,
            ],
        ]);
    }

    private function prepareProducts(Collection $items): array
    {
        $products = [];
        /** @var OrderItem $item */
        foreach ($items as $item) {
            $products[] = (new InvoiceItem())
                ->title($item->name ?? $item->title ?? $item->buyable->name ?? $item->buyable->title)
                ->description($item->description ?? '')
                ->pricePerUnit($item->price/100)
                ->quantity($item->quantity)
                ->discount($item->discount ?? 0);
        }

        return $products;
    }

    private function prepareNote(Order $order): string
    {
        return $order->note ?? '';
    }

    private function filter_filename(string $name): string
    {
        $name = str_replace(array_merge(
            array_map('chr', range(0, 31)),
            array('<', '>', ':', '"', '/', '\\', '|', '?', '*', ' ')
        ), '', $name);
        $ext = pathinfo($name, PATHINFO_EXTENSION);
        $name = mb_strcut(pathinfo($name, PATHINFO_FILENAME), 0, 255 - ($ext ? strlen($ext) + 1 : 0), mb_detect_encoding($name)) . ($ext ? '.' . $ext : '');

        return $name;
    }
}
