@extends('apps::dashboard.layouts.app')
@section('title', __('order::dashboard.orders.show.title'))
@section('content')
    <style type="text/css">
        .table>thead>tr>th {
            border-bottom: none !important;
        }
    </style>
    <style type="text/css" media="print">
        @page {
            size: auto;
            margin: 0;
        }

        @media print {
            a[href]:after {
                content: none !important;
            }

            .contentPrint {
                width: 100%;
                /* font-family: tahoma; */
                font-size: 16px;
            }

            .invoice-body td.notbold {
                padding: 2px;
            }

            h2.invoice-title.uppercase {
                margin-top: 0px;
            }

            .invoice-content-2 {
                background-color: #fff;
                padding: 5px 20px;
            }

            .invoice-content-2 .invoice-cust-add,
            .invoice-content-2 .invoice-head {
                margin-bottom: 0px;
            }

            .no-print,
            .no-print * {
                display: none !important;
            }

        }
    </style>

    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="page-bar">
                <ul class="page-breadcrumb">
                    <li>
                        <a href="{{ url(route('dashboard.home')) }}">{{ __('apps::dashboard.home.title') }}</a>
                        <i class="fa fa-circle"></i>
                    </li>
                    <li>
                        <a href="{{ url(route('dashboard.' . $ordersRouteName . '.index')) }}">
                            {{ __('order::dashboard.orders.index.title') }}
                        </a>
                        <i class="fa fa-circle"></i>
                    </li>
                    <li>
                        <a href="#">{{ __('order::dashboard.orders.show.title') }}</a>
                    </li>
                </ul>
            </div>

            <h1 class="page-title"></h1>

            <div class="row">
                <div class="col-md-12">
                    <div class="no-print">
                        <div class="col-md-3">
                            <ul class="ver-inline-menu tabbable margin-bottom-10">

                                <li class="active">
                                    <a data-toggle="tab" href="#customer_order">
                                        <i class="fa fa-cog"></i> {{ __('order::dashboard.orders.show.invoice_customer') }}
                                    </a>
                                    <span class="after"></span>
                                </li>

                                @permission('show_order_details_tab')
                                    <li>
                                        <a data-toggle="tab" href="#order">
                                            <i class="fa fa-cog"></i> {{ __('order::dashboard.orders.show.invoice_details') }}
                                        </a>
                                        <span class="after"></span>
                                    </li>
                                @endpermission

                                @permission('show_order_change_status_tab')
                                    <li class="">
                                        <a data-toggle="tab" href="#drivers">
                                            <i class="fa fa-cog"></i>
                                            {{ __('order::dashboard.orders.show.change_order_status') }}
                                        </a>
                                        <span class="after"></span>
                                    </li>
                                @endpermission

                            </ul>
                        </div>
                    </div>
                    <div class="col-md-9 contentPrint">
                        <div class="tab-content">

                            <div class="tab-pane active" id="customer_order">
                                <div class="invoice-content-2 bordered">
                                    <div class="row invoice-head">
                                        <div class="col-md-2 col-xs-2">
                                            <div class="invoice-logo">
                                                <center>
                                                    <img src="{{ url(config('setting.logo')) }}" class="img-responsive"
                                                        alt=""
                                                        style="width: 100px; height: auto; margin-bottom: 10px" />
                                                    <span
                                                        style="background-color: {{ json_decode($order->orderStatus->color_label)->value }}; color: #000000; border-radius: 25px; padding: 2px 14px; float: none;">
                                                        {{ $order->orderStatus->translate(locale())->title }}
                                                    </span>
                                                </center>
                                            </div>
                                        </div>

                                        @if ($order->orderAddress != null)
                                            <div class="col-md-5 col-xs-5">
                                                @if (!is_null($order->orderAddress->state))
                                                    <span class="bold uppercase">
                                                        {{ $order->orderAddress->state->city->translate(locale())->title }}
                                                        /
                                                        {{ $order->orderAddress->state->translate(locale())->title }}
                                                    </span>
                                                @endif
                                                <br />

                                                @if ($order->orderAddress->governorate)
                                                    <span
                                                        class="bold">{{ __('order::dashboard.orders.show.address.governorate') }}
                                                        :
                                                    </span>
                                                    {{ $order->orderAddress->governorate }}
                                                    <br />
                                                @endif

                                                @if ($order->orderAddress->block)
                                                    <span
                                                        class="bold">{{ __('order::dashboard.orders.show.address.block') }}
                                                        :
                                                    </span>
                                                    {{ $order->orderAddress->block }}
                                                    <br />
                                                @endif

                                                @if ($order->orderAddress->district)
                                                    <span
                                                        class="bold">{{ __('order::dashboard.orders.show.address.district') }}
                                                        :
                                                    </span>
                                                    {{ $order->orderAddress->district }}
                                                    <br />
                                                @endif

                                                @if ($order->orderAddress->street)
                                                    <span
                                                        class="bold">{{ __('order::dashboard.orders.show.address.street') }}
                                                        :
                                                    </span>
                                                    {{ $order->orderAddress->street }}
                                                    <br />
                                                @endif

                                                @if ($order->orderAddress->building)
                                                    <span
                                                        class="bold">{{ __('order::dashboard.orders.show.address.building') }}
                                                        :
                                                    </span>
                                                    {{ $order->orderAddress->building }}
                                                    <br />
                                                @endif

                                                @if ($order->orderAddress->floor)
                                                    <span
                                                        class="bold">{{ __('order::dashboard.orders.show.address.floor') }}
                                                        :
                                                    </span>
                                                    {{ $order->orderAddress->floor }}
                                                    <br />
                                                @endif

                                                @if ($order->orderAddress->flat)
                                                    <span
                                                        class="bold">{{ __('order::dashboard.orders.show.address.flat') }}
                                                        : </span>
                                                    {{ $order->orderAddress->flat }}
                                                    <br />
                                                @endif

                                                <span
                                                    class="bold">{{ __('order::dashboard.orders.show.address.details') }}
                                                    :
                                                </span>
                                                {{ $order->orderAddress->address ?? '---' }}
                                            </div>
                                        @endif

                                        <div class="col-md-5 col-xs-5">
                                            <div class="company-address">
                                                <h6 class="uppercase">#{{ $order['id'] }}</h6>
                                                <h6 class="uppercase">
                                                    {{ date('Y-m-d / H:i:s', strtotime($order->created_at)) }}</h6>
                                                <span class="bold">
                                                    {{ __('order::dashboard.orders.show.user.username') }} :
                                                </span>
                                                {{ $order->orderAddress->username ?? '---' }}
                                                <br />
                                                <span class="bold">
                                                    {{ __('order::dashboard.orders.show.user.mobile') }} :
                                                </span>
                                                {{ $order->orderAddress ? $order->orderAddress->mobile : $order->unknownOrderAddress->receiver_mobile }}
                                                <br />
                                                <span class="bold">
                                                    {{ __('transaction::dashboard.orders.show.transaction.method') }} :
                                                </span>
                                                {{ ucfirst($order->transactions->method) }}
                                                {{-- <span class="bold">
                                                  {{__('order::dashboard.orders.show.address.civil_id')}} :
                                            </span>
                                            {{ $order->orderAddress->civil_id }} --}}
                                                <br />
                                            </div>
                                        </div>

                                        <div class="row invoice-body">
                                            <div class="col-xs-12 table-responsive">
                                                <br>
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th class="invoice-title uppercase text-left">
                                                                {{ __('order::dashboard.orders.show.items.title') }}
                                                            </th>
                                                            <th class="invoice-title uppercase text-left">
                                                                {{ __('order::dashboard.orders.show.items.price') }}
                                                            </th>
                                                            <th class="invoice-title uppercase text-left">
                                                                {{ __('order::dashboard.orders.show.items.qty') }}
                                                            </th>
                                                            <th class="invoice-title uppercase text-left">
                                                                {{ __('order::dashboard.orders.show.items.total') }}
                                                            </th>
                                                            @if ($order->orderCoupons && !empty($order->orderCoupons->products))
                                                                <th class="invoice-title uppercase text-left">
                                                                    {{ __('order::dashboard.orders.show.items.coupon_discount') }}
                                                                </th>
                                                            @endif
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $customSubTotal = 0;
                                                        @endphp
                                                        @foreach ($order->allProducts as $product)
                                                            @if (isset($product->product_variant_id) && !empty($product->product_variant_id))
                                                                <tr>
                                                                    <td class="text-left sbold">
                                                                        <a
                                                                            href="{{ route('dashboard.products.edit', $product->variant->product->id) }}">
                                                                            @if ($product->variant->product->vendor)
                                                                                {{ $product->variant->product->vendor->translate(locale())->title }}
                                                                                <br>
                                                                            @endif
                                                                            {{ generateVariantProductData($product->variant->product, $product->product_variant_id, $product->variant->productValues->pluck('option_value_id')->toArray())['name'] }}
                                                                        </a>
                                                                        @if ($product->notes)
                                                                            <h5>
                                                                                <b>#
                                                                                    {{ __('order::dashboard.orders.show.items.notes') }}</b>
                                                                                : {{ $product->notes }}
                                                                            </h5>
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-left sbold"> {{ $product->sale_price }}
                                                                    </td>
                                                                    <td class="text-left sbold"> {{ $product->qty }} </td>
                                                                    <td class="text-left sbold"> {{ $product->total }}</td>
                                                                    @if ($order->orderCoupons &&
                                                                        !empty($order->orderCoupons->products) &&
                                                                        in_array($product->variant->product->id, $order->orderCoupons->products ?? []))
                                                                        <td class="text-left sbold">
                                                                            @if ($order->orderCoupons->discount_type == 'value')
                                                                                <span>{{ $order->orderCoupons->discount_value }}
                                                                                    {{ __('apps::frontend.master.kwd') }}</span>
                                                                            @else
                                                                                <span>{{ round($order->orderCoupons->discount_percentage, 1) }}
                                                                                    %</span>
                                                                            @endif
                                                                        </td>
                                                                    @endif
                                                                </tr>
                                                            @else
                                                                @php
                                                                    if ($order->orderCoupons && empty($order->orderCoupons->products)) {
                                                                        if (!empty($product->add_ons_option_ids)) {
                                                                            $customSubTotal += (floatval(json_decode($product->add_ons_option_ids)->total_amount) + floatval($product->sale_price)) * intval($product->qty);
                                                                        } else {
                                                                            $customSubTotal += $product->total;
                                                                        }
                                                                    }
                                                                @endphp
                                                                <tr>
                                                                    <td class="notbold text-left">
                                                                        <a
                                                                            href="{{ route('dashboard.products.edit', $product->product->id) }}">
                                                                            @if ($product->product->vendor)
                                                                                {{ $product->product->vendor->translate(locale())->title }}
                                                                                <br>
                                                                            @endif
                                                                            {{ $product->product->translate(locale())->title }}
                                                                            /
                                                                            {{ $product->product->sku }}
                                                                        </a>
                                                                        @if ($product->notes)
                                                                            <h5>
                                                                                <b>#
                                                                                    {{ __('order::dashboard.orders.show.items.notes') }}</b>
                                                                                : {{ $product->notes }}
                                                                            </h5>
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-left notbold">
                                                                        {{ $product->sale_price }}
                                                                        {{-- @if (!empty($product->add_ons_option_ids))
                                                                        {{ $product->sale_price }}
                                                            +
                                                            {{ json_decode($product->add_ons_option_ids)->total_amount }}
                                                            @else
                                                            {{ $product->sale_price }}
                                                            @endif --}}
                                                                    </td>
                                                                    <td class="text-left notbold"> {{ $product->qty }}
                                                                    </td>
                                                                    <td class="text-left notbold">
                                                                        @if (!empty($product->add_ons_option_ids))
                                                                            {{ (floatval(json_decode($product->add_ons_option_ids)->total_amount) + floatval($product->sale_price)) * intval($product->qty) }}
                                                                        @else
                                                                            {{ $product->total }}
                                                                        @endif
                                                                    </td>
                                                                    @if ($order->orderCoupons &&
                                                                        !empty($order->orderCoupons->products) &&
                                                                        in_array($product->product->id, $order->orderCoupons->products ?? []))
                                                                        <td class="text-left sbold">
                                                                            @if ($order->orderCoupons->discount_type == 'value')
                                                                                <span>{{ $order->orderCoupons->discount_value }}
                                                                                    {{ __('apps::frontend.master.kwd') }}</span>
                                                                            @else
                                                                                <span>{{ round($order->orderCoupons->discount_percentage, 1) }}
                                                                                    %</span>
                                                                            @endif
                                                                        </td>
                                                                    @endif
                                                                </tr>

                                                                @if (!is_null($product->add_ons_option_ids) && !empty($product->add_ons_option_ids))
                                                                    @foreach (json_decode($product->add_ons_option_ids)->data as $key => $addons)
                                                                        @foreach ($addons->options as $k => $option)
                                                                            <tr>
                                                                                <td>
                                                                                    <b>#
                                                                                        {{ getAddonsTitle($addons->id) }}</b>
                                                                                    - {{ getAddonsOptionTitle($option) }}
                                                                                </td>
                                                                                <td class="text-left notbold">
                                                                                    {{ getOrderAddonsOptionPrice(json_decode($product->add_ons_option_ids), $option) }}
                                                                                </td>
                                                                                <td class="text-left notbold">1</td>
                                                                                <td class="text-left notbold">
                                                                                    {{ getOrderAddonsOptionPrice(json_decode($product->add_ons_option_ids), $option) }}
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    @endforeach
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    </tbody>
                                                    <thead>

                                                        {{-- @if (isset(json_decode($product->add_ons_option_ids)->total_amount) && !empty(json_decode($product->add_ons_option_ids)->total_amount))
                                                    <tr>
                                                        <th class="text-left bold">
                                                            {{__('order::dashboard.orders.show.order.addons_total')}}
                                                    </th>
                                                    <th colspan="3" class="text-left bold">
                                                        {{ json_decode($product->add_ons_option_ids)->total_amount }}
                                                    </th>
                                                    </tr>
                                                    @endif --}}

                                                        <tr>
                                                            <th class="text-left bold">
                                                                {{ __('order::dashboard.orders.show.order.subtotal') }}
                                                            </th>
                                                            <th></th>
                                                            <th></th>
                                                            @if ($order->orderCoupons && empty($order->orderCoupons->products))
                                                                <th class="text-left bold"> {{ $customSubTotal }} </th>
                                                            @else
                                                                <th class="text-left bold"> {{ $order->subtotal }} </th>
                                                            @endif
                                                        </tr>
                                                        @if ($order->orderCoupons && empty($order->orderCoupons->products))
                                                            <tr style="border-top: 2px solid #d6dae0;">
                                                                <th class="text-left bold">
                                                                    {{ __('order::dashboard.orders.show.order.coupon_discount') }}
                                                                </th>
                                                                <th></th>
                                                                <th></th>
                                                                <th class="text-left bold">
                                                                    @if ($order->orderCoupons->discount_type == 'value')
                                                                        {{ $order->orderCoupons->discount_value }}
                                                                    @else
                                                                        {{ $order->orderCoupons->discount_percentage }} %
                                                                    @endif
                                                                </th>
                                                            </tr>
                                                        @endif
                                                        <tr
                                                            style="{{ is_null($order->orderCoupons) || !empty($order->orderCoupons->products) ? 'border-top: 2px solid #d6dae0;' : '' }}">
                                                            <th class="text-left bold">
                                                                {{ __('order::dashboard.orders.show.order.shipping') }}
                                                            </th>
                                                            <th></th>
                                                            <th></th>
                                                            <th class="text-left bold">{{ $order->shipping }}</th>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-left bold">
                                                                {{ __('order::dashboard.orders.show.order.total') }}
                                                            </th>
                                                            <th></th>
                                                            <th></th>
                                                            <th class="text-left bold">{{ $order->total }}</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 col-xs-12">
                                                <div style="margin: 10px;">
                                                    <b>{{ __('order::dashboard.orders.show.notes') }}
                                                        : </b>
                                                    <span>{{ $order->notes ?? '---' }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- <hr> --}}
                                        {{-- <div class="row invoice-subtotal">
                                            <div class="col-xs-2">
                                                <h4 class="text-center notbold">{{__('order::dashboard.orders.show.order.subtotal')}}
                                    </h4>
                                    <p class="text-center notbold">{{ $order->subtotal }}</p>
                                </div>
                                <div class="col-xs-2">
                                    <h4 class="text-center notbold">{{__('order::dashboard.orders.show.order.total')}}
                                    </h4>
                                    <p class="text-center notbold">{{ $order->total }}</p>
                                </div>
                            </div> --}}

                                    </div>
                                </div>
                            </div>

                            @permission('show_order_details_tab')
                                <div class="tab-pane" id="order">
                                    <div class="invoice">

                                        <div class="row">
                                            <h4>{{ __('transaction::dashboard.orders.show.transactions') }}</h4>
                                            <div class="col-xs-12 table-responsive">
                                                <table class="table table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th class="invoice-title uppercase text-center">
                                                                {{ __('transaction::dashboard.orders.show.transaction.payment_id') }}
                                                            </th>
                                                            <th class="invoice-title uppercase text-center">
                                                                {{ __('transaction::dashboard.orders.show.transaction.track_id') }}
                                                            </th>
                                                            <th class="invoice-title uppercase text-center">
                                                                {{ __('transaction::dashboard.orders.show.transaction.method') }}
                                                            </th>
                                                            <th class="invoice-title uppercase text-center">
                                                                {{ __('transaction::dashboard.orders.show.transaction.result') }}
                                                            </th>
                                                            <th class="invoice-title uppercase text-center">
                                                                {{ __('transaction::dashboard.orders.show.transaction.ref') }}
                                                            </th>
                                                            <th class="invoice-title uppercase text-center">
                                                                {{ __('transaction::dashboard.orders.show.transaction.tran_id') }}
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td class="text-center sbold">
                                                                {{ $order->transactions->payment_id }}</td>
                                                            <td class="text-center sbold">
                                                                {{ $order->transactions->track_id }}</td>
                                                            <td class="text-center sbold">
                                                                {{ $order->transactions->method }}
                                                            </td>
                                                            <td class="text-center sbold"> {{ $order->transactions->result }}
                                                            </td>
                                                            <td class="text-center sbold"> {{ $order->transactions->ref }}
                                                            </td>
                                                            <td class="text-center sbold"> {{ $order->transactions->tran_id }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        {{-- <hr>
                                    <div class="row">
                                        <h3>{{__('order::dashboard.orders.show.items.companies.index')}}</h3>

                        @if (count($order->companies) > 0)
                        <div class="col-xs-12 table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th class="invoice-title uppercase text-center">
                                            {{__('order::dashboard.orders.show.items.companies.name')}}
                                        </th>
                                        <th class="invoice-title uppercase text-center">
                                            {{__('order::dashboard.orders.show.items.companies.availabilities')}}
                                        </th>
                                        <th class="invoice-title uppercase text-center">
                                            {{__('order::dashboard.orders.show.items.companies.delivery')}}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->companies as $k => $item)
                                    <tr>
                                        <td>{{ ++$k }}</td>
                                        <td class="text-center sbold">
                                            {{ $item->translate(locale())->name }}
                                        </td>
                                        <td class="text-center sbold">
                                            @if ($item->pivot->availabilities)
                                            <span>{{ __('company::dashboard.companies.availabilities.days.'.\GuzzleHttp\json_decode($item->pivot->availabilities)->day_code) }}</span>
                                            <span> /
                                                {{ \GuzzleHttp\json_decode($item->pivot->availabilities)->full_date }}</span>
                                            @else
                                            <span>-&#45;&#45;</span>
                                            @endif
                                        </td>
                                        <td class="text-center sbold"> {{ $item->pivot->delivery }} </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <b>{{__('order::dashboard.orders.show.items.addons.no_available_addons')}}</b>
                        @endif

                    </div> --}}

                                    </div>
                                </div>
                            @endpermission

                            @permission('show_order_change_status_tab')
                                <div class="tab-pane" id="drivers">
                                    <div class="no-print">
                                        <div class="row">

                                            <div class="col-md-6">

                                                <form id="updateForm" method="POST"
                                                    action="{{ url(route('dashboard.orders.update', $order['id'])) }}"
                                                    enctype="multipart/form-data" class="horizontal-form">
                                                    @csrf
                                                    <input name="_method" type="hidden" value="PUT">

                                                    <div class="form-group">
                                                        <label>
                                                            {{ __('order::dashboard.orders.show.drivers.title') }}
                                                        </label>
                                                        <select name="user_id" class="form-control">
                                                            <option value="">
                                                                --- {{ __('order::dashboard.orders.show.drivers.title') }}
                                                                ---
                                                            </option>
                                                            @foreach ($drivers as $driver)
                                                                <option value="{{ $driver->id }}"
                                                                    @if ($order->driver) {{ $order->driver->user_id == $driver->id ? 'selected' : '' }} @endif>
                                                                    {{ $driver->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>
                                                            {{ __('order::dashboard.orders.show.status') }}
                                                        </label>
                                                        <select name="order_status" id="single" class="form-control">
                                                            <option value="">--- Select ---</option>
                                                            @foreach ($statuses as $status)
                                                                <option value="{{ $status->id }}"
                                                                    {{ $order->order_status_id == $status->id ? 'selected' : '' }}>
                                                                    {{ $status->translate(locale())->title }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>
                                                            {{ __('order::dashboard.orders.show.order_notes') }}
                                                        </label>
                                                        <textarea class="form-control" name="order_notes" rows="8" cols="80">{{ $order->order_notes }}</textarea>
                                                    </div>

                                                    <div id="result" style="display: none"></div>
                                                    <div class="progress-info" style="display: none">
                                                        <div class="progress">
                                                            <span class="progress-bar progress-bar-warning"></span>
                                                        </div>
                                                        <div class="status" id="progress-status"></div>
                                                    </div>
                                                    <div class="form-group">
                                                        <button type="submit" id="submit" class="btn green btn-lg">
                                                            {{ __('apps::dashboard.general.edit_btn') }}
                                                        </button>
                                                    </div>

                                                </form>

                                            </div>

                                            <div class="col-md-6">
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th class="invoice-title uppercase text-center">
                                                                    {{ __('order::dashboard.orders.show.order_history.order_status') }}
                                                                </th>
                                                                <th class="invoice-title uppercase text-center">
                                                                    {{ __('order::dashboard.orders.show.order_history.updated_by') }}
                                                                </th>
                                                                <th class="invoice-title uppercase text-center">
                                                                    {{ __('order::dashboard.orders.show.order_history.date') }}
                                                                </th>
                                                                {{-- <th class="invoice-title uppercase text-center">
                                                            {{__('order::dashboard.orders.show.order_history.btn_delete')}}
                                            </th> --}}
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($order->orderStatusesHistory as $k => $history)
                                                                <tr id="orderHistory-{{ $history->pivot->id }}">
                                                                    <td class="text-center sbold">
                                                                        {{ $history->translate(locale())->title }}
                                                                    </td>
                                                                    <td class="text-center sbold">
                                                                        {{ is_null($history->pivot->user_id) ? '---' : optional(\Modules\User\Entities\User::find($history->pivot->user_id))->name ?? '---' }}
                                                                    </td>
                                                                    <td class="text-center sbold">
                                                                        {{ $history->pivot->created_at }}</td>
                                                                    {{-- <td>
                                                                <button type="button" class="btn btn-danger"
                                                                        onclick="deleteOrderHistory('{{ $history->pivot->id }}')">
                                            <i class="fa fa-trash-o"></i>
                                            </button>
                                            </td> --}}
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            @endpermission

                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-4">
                        <a class="btn btn-lg blue hidden-print margin-bottom-5" onclick="javascript:window.print();">
                            {{ __('apps::dashboard.general.print_btn') }}
                            <i class="fa fa-print"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

@stop

@section('scripts')
    <script>
        {{-- function deleteOrderHistory(id) {
            $.ajax({
                method: "GET",
                url: '{{ route('dashboard.orders.delete_order_history') }}',
data: {
"id": id,
"_token": '{{ csrf_token() }}',
},
beforeSend: function () {
},
success: function (data) {
// console.log('success::data::', data);
$('#orderHistory-' + id).remove();
},
error: function (data) {
displayErrorsMsg(data);
},
complete: function (data) {
var getJSON = $.parseJSON(data.responseText);
displaySuccessToaster(getJSON[1]);

// console.log('data::', data);
// console.log('getJSON::', getJSON);
},
});
} --}}
    </script>
@endsection
