@extends('apps::frontend.layouts.master')
@section('title', __('order::frontend.orders.invoice.details_title') )
@section('content')

    <div class="container">
        <div class="invoice-page invoice-style2">
            <div class="invoice-conent">
                <h1 class="invoice-head">{{ __('order::frontend.orders.invoice.title') }}</h1>
                <div class="invoice-head-rec">
                    <div class="row">
                        <div class="col-md-8 col-4">
                            <img
                                src="{{ config('setting.logo') ? url(config('setting.logo')) : url('frontend/images/logo.png') }}"
                                class="img-fluid">
                        </div>
                        <div class="col-md-4 col-8">
                            <address class="norm">
                                <p class="d-flex">
                                    <b class="flex-1">{{ __('order::frontend.orders.invoice.order_id') }} </b>{{ $order->id }}
                                </p>
                                <p class="d-flex">
                                    <b class="flex-1">{{ __('order::frontend.orders.invoice.date') }}</b>{{ $order->created_at }}
                                <p>
                                <p class="d-flex">
                                    <b class="flex-1">{{ __('order::frontend.orders.invoice.method') }}</b>
                                    @if($order->transactions->method == 'cash')
                                        {{ __('order::frontend.orders.invoice.cash') }}
                                    @else
                                        {{ __('order::frontend.orders.invoice.online') }}
                                    @endif
                                </p>
                            </address>
                        </div>
                    </div>
                </div>

                <div class="invoice-body">
                    <div class="row">

                        {{--<div class="col-md-4">
                            @if(auth()->user())
                                <h1>{{ __('order::frontend.orders.invoice.client_address.sender') }}</h1>
                                <address class="norm">
                                    <p class="d-flex">
                                        <b class="flex-1">{{ __('order::frontend.orders.invoice.client_address.name') }}
                                            : </b>{{ auth()->user()->name }}
                                    </p>
                                    <p class="d-flex">
                                        <b class="flex-1">{{ __('order::frontend.orders.invoice.client_address.state') }}
                                            :</b> Alexandria, Egypt
                                    <p>
                                    <p class="d-flex">
                                        <b class="flex-1">{{ __('order::frontend.orders.invoice.client_address.mobile') }}
                                            :</b> {{ auth()->user()->mobile }}
                                    </p>
                                </address>
                            @endif
                        </div>
                        <div class="col-md-4"></div>--}}

                        <div class="col-md-4">
                            <h1>{{ __('order::frontend.orders.invoice.client_address.receiver') }}</h1>
                            @if($order->unknownOrderAddress)
                                <address class="norm">
                                    <p class="d-flex">
                                        <b class="flex-1">{{ __('order::frontend.orders.invoice.client_address.name') }}
                                            : </b>{{ $order->unknownOrderAddress->receiver_name }}
                                    </p>
                                    <p class="d-flex">
                                        <b class="flex-1">{{ __('order::frontend.orders.invoice.client_address.state') }}
                                            :</b>{{ $order->unknownOrderAddress->state->translate(locale())->title }}
                                    <p>
                                    <p class="d-flex">
                                        <b class="flex-1">{{ __('order::frontend.orders.invoice.client_address.mobile') }}
                                            :</b>{{ $order->unknownOrderAddress->receiver_mobile }}
                                    </p>
                                </address>
                            @else
                                <address class="norm">
                                    <p class="d-flex">
                                        <b class="flex-1">{{ __('order::frontend.orders.invoice.client_address.state') }}
                                            : </b>{{ $order->orderAddress->state->translate(locale())->title }}
                                    </p>
                                    <p class="d-flex">
                                        <b class="flex-1">{{ __('order::frontend.orders.invoice.client_address.block') }}
                                            :</b>{{ $order->orderAddress->block }}
                                    <p>
                                    <p class="d-flex">
                                        <b class="flex-1">{{ __('order::frontend.orders.invoice.client_address.building') }}
                                            :</b>{{ $order->orderAddress->building }}
                                    </p>

                                    @if(!empty($order->orderAddress->address))
                                        <p class="d-flex">
                                            <b class="flex-1">{{ __('order::frontend.orders.invoice.client_address.details') }}
                                                :</b>{{ $order->orderAddress->address }}
                                        </p>
                                    @endif

                                </address>
                            @endif
                        </div>

                    </div>

                    <table class="inventory">
                        <thead>
                        <tr>
                            <th><span> #</span></th>
                            <th><span>{{ __('order::frontend.orders.invoice.product_title') }}</span></th>
                            <th><span>{{ __('order::frontend.orders.invoice.product_qty') }}</span></th>
                            <th><span>{{ __('order::frontend.orders.invoice.product_price') }}</span></th>
                        </tr>
                        </thead>
                        <tbody>

                        @if(count($order->orderProducts) > 0)
                            @foreach ($order->orderProducts as $key => $orderProduct)
                                <tr class="{{ ++$key % 2 == 0 ? 'even' : '' }}">
                                    <td><span>{{ $key }}</span></td>
                                    @if(isset($orderProduct->product_variant_id) && !empty($orderProduct->product_variant_id))
                                        <td>
                                            <span>{{ generateVariantProductData($orderProduct->variant->product, $orderProduct->product_variant_id, $orderProduct->variant->productValues->pluck('option_value_id')->toArray())['name'] }}</span>
                                        </td>
                                    @else
                                        <td><span>{{ $orderProduct->product->translate(locale())->title }}</span></td>
                                    @endif
                                    <td><span>{{ $orderProduct->qty }}</span></td>
                                    <td><span>{{ $orderProduct->price }}</span>
                                        <span data-prefix>{{ __('apps::frontend.master.kwd') }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        @endif

                        </tbody>
                    </table>

                    <table class="balance">
                        <tr>
                            <th><span>{{ __('order::frontend.orders.invoice.subtotal') }}</span></th>
                            <td>
                                <span>{{ $order->subtotal }}</span>
                                <span data-prefix>{{ __('apps::frontend.master.kwd') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th><span>{{ __('order::frontend.orders.invoice.shipping') }}</span></th>
                            <td>
                                <span>{{ $order->shipping }}</span>
                                <span data-prefix>{{ __('apps::frontend.master.kwd') }}</span>
                            </td>
                        </tr>
                        <tr class="price">
                            <th><span>{{ __('order::frontend.orders.invoice.total') }}</span></th>
                            <td>
                                <span>{{ $order->total }}</span>
                                <span data-prefix>{{ __('apps::frontend.master.kwd') }}</span>
                            </td>
                        </tr>
                    </table>

                </div>
            </div>
            <div class="invoice-footer">
                <button class="btn btn-them print-invoice">
                    <i class="ti-printer"></i> {{ __('order::frontend.orders.invoice.btn.print') }}</button>
            </div>
        </div>
    </div>


@endsection

@section('externalJs')

    <script></script>

@endsection
