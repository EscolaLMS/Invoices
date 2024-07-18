<?php

namespace EscolaLms\Invoices\Http\Requests;

use EscolaLms\Cart\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class InvoicesReadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('view', $this->getOrder());
    }

    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();
        $this->merge(['id' => $this->route('id')]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'id' => [
                'integer',
                'required',
                Rule::exists(Order::class, 'id'),
            ],
        ];
    }

    public function getParamId(): int
    {
        /** @var int $id */
        $id = $this->route('id');
        return $id;
    }

    public function getOrder(): Order
    {
        /** @var Order $order */
        $order = Order::findOrFail($this->getParamId());
        return $order;
    }
}
