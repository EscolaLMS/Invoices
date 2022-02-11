<?php

namespace EscolaLms\Invoices\Services;

use EscolaLms\Cart\Models\Order;
use EscolaLms\Cart\Models\OrderItem;
use EscolaLms\Invoices\Services\Contracts\InvoicesServiceContract;
use Illuminate\Database\Eloquent\Collection;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Facades\Invoice;
use LaravelDaily\Invoices\Invoice as InvoiceModel;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Classes\InvoiceItem;

class InvoicesService implements InvoicesServiceContract
{
    public function sandInvoices(Order $order): void
    {
        $invoice = $this->getInvoices($order);

        $link = $invoice->url();
    }

    public function saveInvoices(Order $order): void
    {
        $invoice = $this->getInvoices($order);

        $invoice->save('public');
    }

    public function getInvoices(Order $order): InvoiceModel
    {
        $customer = $this->prepareCustomer($order);
        $items = $this->prepareProducts($order->items);
        $notes = $this->prepareNote($order);

        $invoice = $this->setParamsFromConfig(Invoice::make());

        $invoice->status($order->status_name)
            ->buyer($customer)
            ->addItems($items)
            ->notes($notes)
            ->filename($customer->name.'_fv_'.$order->id);

        return $invoice;
    }

    private function setParamsFromConfig(InvoiceModel $invoice): InvoiceModel
    {
        $client = $this->prepareClient();

        $invoice->series('BIG')
            ->sequence(667)
            ->serialNumberFormat('{SEQUENCE}/{SERIES}')
            ->seller($client)
            ->date(now()->subWeeks(3)) //config
            ->dateFormat('m/d/Y')
            ->payUntilDays(14)
            ->currencySymbol('$')
            ->currencyCode('USD')
            ->currencyFormat('{SYMBOL}{VALUE}')
            ->currencyThousandsSeparator('.')
            ->currencyDecimalPoint(',')
            ->logo(public_path('vendor/invoices/sample-logo.png'));

        return $invoice;
    }

    private function prepareClient(): Party
    {
        return new Party([
            'name'          => 'Roosevelt Lloyd',
            'phone'         => '(520) 318-9486',
            'custom_fields' => [
                'note'        => 'IDDQD',
                'business id' => '365#GG',
            ],
        ]);
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
        /*foreach ($items as $item) {
            $products[] = (new InvoiceItem())
                ->title($item->name ?? $item->title ?? $item->buyable->name ?? $item->buyable->title)
                ->description($item->description ?? '')
                ->pricePerUnit($item->price/100)
                ->quantity($item->quantity)
                ->discount($item->discount ?? 0);
        }

        return $products;*/

        $items = [
            (new InvoiceItem())
                ->title('Service 1')
                ->description('Your product or service description')
                ->pricePerUnit(47.79)
                ->quantity(2)
                ->discount(10),
            (new InvoiceItem())->title('Service 2')->pricePerUnit(71.96)->quantity(2),
            (new InvoiceItem())->title('Service 3')->pricePerUnit(4.56),
            (new InvoiceItem())->title('Service 4')->pricePerUnit(87.51)->quantity(7)->discount(4)->units('kg'),
            (new InvoiceItem())->title('Service 5')->pricePerUnit(71.09)->quantity(7)->discountByPercent(9),
            (new InvoiceItem())->title('Service 6')->pricePerUnit(76.32)->quantity(9),
            (new InvoiceItem())->title('Service 7')->pricePerUnit(58.18)->quantity(3)->discount(3),
            (new InvoiceItem())->title('Service 8')->pricePerUnit(42.99)->quantity(4)->discountByPercent(3),
            (new InvoiceItem())->title('Service 9')->pricePerUnit(33.24)->quantity(6)->units('m2'),
            (new InvoiceItem())->title('Service 11')->pricePerUnit(97.45)->quantity(2),
            (new InvoiceItem())->title('Service 12')->pricePerUnit(92.82),
            (new InvoiceItem())->title('Service 13')->pricePerUnit(12.98),
            (new InvoiceItem())->title('Service 14')->pricePerUnit(160)->units('hours'),
            (new InvoiceItem())->title('Service 15')->pricePerUnit(62.21)->discountByPercent(5),
            (new InvoiceItem())->title('Service 16')->pricePerUnit(2.80),
            (new InvoiceItem())->title('Service 17')->pricePerUnit(56.21),
            (new InvoiceItem())->title('Service 18')->pricePerUnit(66.81)->discountByPercent(8),
            (new InvoiceItem())->title('Service 19')->pricePerUnit(76.37),
            (new InvoiceItem())->title('Service 20')->pricePerUnit(55.80),
        ];
        return $items;
    }

    private function prepareNote(Order $order): string
    {
        return $order->note ?? '';
    }
}
