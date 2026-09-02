<div class="container-fluid">
    <div class="invisible" wire:loading.class="visible" wire:loading.class.remove="invisible">
        <div style="position: fixed; top: 10px; left: 0; width: 100%; z-index: 9999;">
            <div class="text-center">
                <div class="spinner-border animate-spin inline-block w-8 h-8 border-4 rounded-full text-blue-500"
                    role="status"></div>
            </div>
        </div>
    </div>
    @if ($temporaryDeliveryOrder->client)
        <h1>
            <a href='{{ route('clients.show', $temporaryDeliveryOrder->client) }}' class=""><i
                    class="fa fa-info-circle text-secondary"></i></a>
            {{ $temporaryDeliveryOrder->client?->user->name ?? '' }} - {{ __('Shopping') }}
        </h1>
    @endif
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <div class="card-header-title font-size-lg font-weight-normal">
                        {{ $client->user->name ?? '' }}
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <strong>{{ __('Mobile') }}:</strong>
                            {{ $temporaryDeliveryOrder->client?->user->mobile ?? '' }}
                        </li>
                    </ul>
                </div>
            </div>
            <div class="main-card mt-3 card">
                <div class="card-header"><strong>{{ __('Supplier Product') }}</strong></div>
                <ul class="list-group list-group-flush">
                    @forelse ($supplierProducts as $supplierProduct)
                        <li class="list-group-item">
                            <div class="widget-content p-0">
                                <div class="widget-content-wrapper">
                                    <div class="widget-content-left">
                                        <div class="widget-heading">
                                            <strong class="d-block">{{ $supplierProduct->name }}</strong>
                                            <span class="d-block">{{ $supplierProduct->price }}
                                                {{ __('EGP') }}</span>
                                        </div>
                                    </div>
                                    <div class="widget-content-right">
                                        <button wire:click="addProductToCart({{ $supplierProduct->id }})"
                                            class="btn btn-success btn-sm">
                                            <i class="fa fa-cart-plus"></i> {{ __('Add to Cart') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item">{{ __('No products.') }}</li>
                    @endforelse
                </ul>

                {{-- <x-form-input name="product_data" type="hidden" label="" :attrs="['autocomplete' => 'off', 'wire:model.live.debounce.1000ms' => 'product_data']" /> --}}
                {{-- <input type="hidden" name="product_data" id="product_data" value="{{ json_encode($temporaryDeliveryOrder->product_data ?? []) }}"> --}}
            </div>

        </div>
        <div class="col-md-8">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <form action="#" class="needs-validation" novalidate>
                        <div class="row">
                            <div class="col-md-6">
                                <livewire:model-autocomplete
                                    model-class="\App\Models\Client"
                                    :search-fields="['user.name', 'user.mobile','user.role']"
                                    :display-field="['user.name', 'user.role']"
                                    input-id="client-search" hidden-input-name="client_id"
                                    :selected-id="$temporaryDeliveryOrder->client_id"
                                    :selected-name="optional($temporaryDeliveryOrder->client?->user)->only([
                                        'name',
                                    ])
                                        ? implode(
                                            ' - ',
                                            array_filter([
                                                $temporaryDeliveryOrder->client?->user?->name,
                                                $temporaryDeliveryOrder->client?->user?->role->label()
                                            ]),
                                        )
                                        : null" emit-event="clientSelected"
                                    label="{{ __('Client') }}"
                                />
                            </div>
                            <div class="col-md-6">
                                {{-- <livewire:supplier-autocomplete :selectedSupplierId="$temporaryDeliveryOrder->supplier_id" :selectedSupplierName="$temporaryDeliveryOrder->supplier?->user?->name" /> --}}
                                <livewire:model-autocomplete
                                    model-class="\App\Models\Supplier"
                                    :search-fields="['user.name', 'user.mobile']"
                                    :display-field="['user.name', 'user.role']" 
                                    input-id="supplier-search" 
                                    hidden-input-name="supplier_id"
                                    :selected-id="$temporaryDeliveryOrder->supplier_id" 
                                    :selected-name="optional($temporaryDeliveryOrder->supplier?->user)->only([
                                        'name',
                                    ])
                                        ? implode(
                                            ' - ',
                                            array_filter([
                                                $temporaryDeliveryOrder->supplier?->user?->name,
                                                $temporaryDeliveryOrder->supplier?->user?->role->label()
                                            ]),
                                        )
                                        : null" 
                                    emit-event="supplierSelected"
                                    label="{{ __('Supplier') }}" />
                            </div>
                            <div class="col-md-3">
                                <x-form-select name="order_channel_id" type="select" label="{{ __('Order Channel') }}"
                                    :options="$orderChannels" :attrs="['wire:model.change' => 'order_channel_id']" />
                            </div>
                            <div class="col-md-3">
                                <x-form-select name="delivery_agent_id" type="select"
                                    label="{{ __('Delivery Agent') }}" :options="$deliveryAgentOptions" :disabledOptions="$disabledAgentIds"
                                    :attrs="['wire:model.change' => 'delivery_agent_id']" />
                            </div>
                            <div class="col-md-3">
                                <x-form-input name="price" type="number" label="{{ __('Price') }}"
                                    :attrs="[
                                        'autocomplete' => 'off',
                                        'wire:model.live.debounce.1000ms' => 'price',
                                        'min' => '0',
                                    ]" :value="$temporaryDeliveryOrder->price ?? ''" />
                            </div>
                            <div class="col-md-3">
                                <x-form-input name="delivery_fees" type="number" label="{{ __('Delivery Fees') }}"
                                    :attrs="[
                                        'autocomplete' => 'off',
                                        'wire:model.live.debounce.1000ms' => 'delivery_fees',
                                    ]" :value="$temporaryDeliveryOrder->delivery_fees ?? ''" />
                            </div>
                        </div>
                        <x-form-textarea name="details" label="{{ __('Details') }}" :attrs="[
                            'rows' => '8',
                            'autocomplete' => 'off',
                            'wire:model.live.debounce.1500ms' => 'details',
                        ]"
                            :value="$temporaryDeliveryOrder->details ?? ''" />
                    </form>
                    <form method="POST"
                        action="{{ route('delivery-orders.shopping-store', $temporaryDeliveryOrder->id) }}" 
                        wire:click="submitOrder" id="cartSubmitForm">

                        @csrf
                        <div class="text-end mt-3">
                            <button type="button" class="btn btn-success">
                                <i class="fa fa-save"></i> {{ __('Confirm Delivery Order') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="mt-3 mb-3">
                <div class="table-responsive mt-4">
                    <table class="table table-bordered align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Product') }}</th>
                                <th>{{ __('Price') }}</th>
                                <th>{{ __('Quantity') }}</th>
                                <th>{{ __('Total') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($temporaryDeliveryOrder->product_data ?? [] as $index => $item)
                                @php
                                    $price = $item['price'] ?? 0;
                                    $qty = $item['quantity'] ?? 1;
                                    $total = $price * $qty;
                                @endphp
                                <tr>
                                    <td>
                                        <a href="{{ route('supplier-products.show', $item['supplier_product_id']) }}"
                                            target="blank">{{ $cartSupplierProducts[$item['supplier_product_id']] }}</a>


                                        @if (isset($clientLastSupplierProducts[$item['supplier_product_id']]))
                                            <br>
                                            اخر سعر شراء:
                                            {{ $clientLastSupplierProducts[$item['supplier_product_id']]['price'] }} من
                                            {{-- use carbon to format date ago --}}

                                            <a href="{{ route('delivery-orders.show', [
                                                $clientLastSupplierProducts[$item['supplier_product_id']]['deliveryOrder']->id,
                                                'supplier-product-id' => $item['supplier_product_id'],
                                            ]) }}"
                                                target="_blank">
                                                {{ optional($clientLastSupplierProducts[$item['supplier_product_id']]['deliveryOrder']->created_at ?? null)->diffForHumans() ?? '-' }}
                                            </a>
                                        @endif
                                    </td>
                                    <td>{{ number_format($price, 2) }}</td>
                                    <td style="width: 120px;">
                                        <x-form-input name="cartProducts.{{ $index }}.quantity" type="number"
                                            label="" class="form-control form-control-sm text-center"
                                            divClass="" :attrs="[
                                                'min' => '0',
                                                'style' => 'width: 80px; margin: auto;',
                                                'wire:model.lazy' => 'cartProducts.' . $index . '.quantity',
                                                'wire:change' => 'updateCartQuantity(' . $index . ')',
                                                'step' => 'number'
                                            ]" 
                                            
                                            />
                                    </td>
                                    <td>{{ number_format($total, 2) }}</td>
                                    <td>
                                        <button wire:click="removeProductFromCart({{ $index }})"
                                            wire:confirm="{{ __('Are you sure you want to remove this item?') }}"
                                            class="btn btn-sm btn-outline-danger">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="text-end fw-bold">{{ __('Grand Total') }}:</td>
                                <td colspan="2" class="text-start fw-bold">
                                    {{ number_format(collect($temporaryDeliveryOrder->product_data)->sum(fn($item) => $item['price'] * $item['quantity']), 2) }}
                                    {{ __('EGP') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    {{-- <form method="POST" action="{{ route('delivery-orders.shopping-store', $temporaryDeliveryOrder->id) }}">
                        @csrf
                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-save"></i> {{ __('Confirm Delivery Order') }}
                            </button>
                        </div>
                    </form> --}}
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('prdocutExists', function(event) {
            alert(event.detail.name + ' this product already exists in the cart.');
        });
    </script>
</div>
