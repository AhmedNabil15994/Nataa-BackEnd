@extends('apps::frontend.layouts.master')
@section('title', __('apps::frontend.products.details.title') )
@section('meta_description', $product->translate(locale())->seo_description ?? '')
@section('meta_keywords', $product->translate(locale())->seo_keywords ?? '')
@section('content')

    <div class="container">
        <div class="page-crumb mt-30">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">

                    <li class="breadcrumb-item">
                        <a href="{{ route('frontend.home') }}">
                            <i class="ti-home"></i> {{ __('apps::frontend.nav.home_page') }}
                        </a>
                    </li>

                    {{--<li class="breadcrumb-item"><a href="index.php?page=category">الأقسام</a></li>
                    <li class="breadcrumb-item"><a href="index.php?page=category">لها</a></li>--}}

                    <li class="breadcrumb-item active text-muted"
                        aria-current="page">{{ __('apps::frontend.products.details.title') }}</li>

                </ol>
            </nav>
        </div>
        <div class="inner-page">
            <div class="row">
                <div class="col-md-5">
                    <div class="main-image sp-wrap" id="mainProductSlider">

                        @if($product->image)
                            <a href="{{ url($product->image) }}">
                                <img src="{{ url($product->image) }}" class="img-responsive" alt="img">
                            </a>
                        @endif

                        @foreach($product->images as $k => $img)
                            <a href="{{ url('uploads/products/' . $img->image) }}">
                                <img src="{{ url('uploads/products/' . $img->image) }}" class="img-responsive"
                                     alt="img">
                            </a>
                        @endforeach

                        @foreach($product->variants as $k => $varPrd)
                            <a href="{{ url($varPrd->image) }}"
                               id="variantPrd-{{ $varPrd->id }}">
                                <img src="{{ url($varPrd->image) }}" class="img-responsive" alt="img">
                            </a>
                        @endforeach

                    </div>
                </div>
                <div class="col-md-7">
                    <div class="product-detials">
                        <div class="product-head media align-items-center">
                            <div class="media-body">
                                <h1>{{ $product->translate(locale())->title }}</h1>

                                @if($product->tags)
                                    @foreach($product->tags as $i => $tag)
                                        <span class="ratings-counting text-muted label-tag-product"
                                              style="background-color: {{ $tag->background ?? '#ddd' }}; color: {{ $tag->color ?? '#fff' }}">{{ $tag->translate(locale())->title }}</span>
                                    @endforeach
                                @endif

                                {{--<div class="ratings">
                                    <span class="rating-starts">
                                        <i class="fa fa-star full"></i>
                                        <i class="fa fa-star full"></i>
                                        <i class="fa fa-star full"></i>
                                        <i class="fa fa-star full"></i>
                                        <i class="fa fa-star empty"></i>
                                    </span>
                                    <span class="ratings-counting text-muted">
                                    15 مراجعة
                                    </span>
                                </div>--}}

                            </div>
                            <div class="text-left">

                                @if(auth()->check() && !in_array($product->id, array_column(auth()->user()->favourites->toArray(), 'id')))
                                    <form class="favourites-form" method="POST">
                                        @csrf
                                        <button type="button" class="btn favo-btn"
                                                onclick="generalAddToFavourites('{{ route('frontend.profile.favourites.store', [ $product->id ]) }}', '{{ $product->id }}')"
                                                id="btnAddToFavourites-{{ $product->id }}">
                                            <i class="fa fa-heart"></i>
                                        </button>
                                    </form>
                                @endif

                            </div>
                        </div>

                        <div class="product-summ-det">
                            {{--<h5> {{ __('catalog::frontend.products.product_variations') }} </h5>--}}

                            @foreach($product->options as $k => $opt)
                                <p class="d-flex">
                                    <span class="d-inline-block right-side">
                                        {{ optional(optional($opt->option)->translate(locale()))->title }}
                                    </span>
                                    <span class="d-inline-block left-side">
                                        <select class="form-control product-var-options"
                                                data-option-id="{{ $opt->option->id }}"
                                                id="prdOption-{{ $opt->id }}"
                                                onchange="getVariationInfo(this, '{{ $product->id }}')">
                                            <option value="">
                                                ---{{ __('catalog::frontend.products.select_option') }}---
                                            </option>
                                            @foreach($opt->productValues->unique('option_value_id') as $i => $optValue)
                                                <option
                                                    value="{{ $optValue->optionValue->id }}" {{ in_array($opt->option->id, $selectedOptions) && in_array($optValue->optionValue->id, $selectedOptionsValue) ? 'selected' : '' }}>
                                                {{ optional(optional($optValue->optionValue)->translate(locale()))->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </span>
                                </p>
                            @endforeach

                        </div>

                        <div class="product-summ-det">
                            <h5> {{ __('catalog::frontend.products.product_information') }} </h5>

                            <div id="skuSection">
                                @if(!is_null($variantPrd))
                                    <p class="d-flex">
                                    <span
                                        class="d-inline-block right-side">{{ __('catalog::frontend.products.sku') }}</span>
                                        <span class="d-inline-block left-side">{{ $variantPrd->sku }}</span>
                                    </p>
                                @else
                                    <p class="d-flex">
                                    <span
                                        class="d-inline-block right-side">{{ __('catalog::frontend.products.sku') }}</span>
                                        <span class="d-inline-block left-side">{{ $product->sku }}</span>
                                    </p>
                                @endif
                            </div>

                            <p class="d-flex">
                                <span
                                    class="d-inline-block right-side">{{ __('catalog::frontend.products.vendor') }}</span>
                                <span class="d-inline-block left-side">
                                    <a href="javascript:;" class="font-weight-bold">
                                        {{ optional(optional($product->vendor)->translate(locale()))->title }}
                                    </a>
                                </span>
                            </p>

                            <div id="remainingQtySection">
                                @if(!is_null($variantPrd))
                                    @if($variantPrd->qty <= 3)
                                        <p class="d-flex">
                                            <span
                                                class="d-inline-block right-side">{{ __('catalog::frontend.products.remaining_qty') }}</span>
                                            <span class="d-inline-block left-side">
                                                    {{ $variantPrd->qty }}
                                            </span>
                                        </p>
                                    @endif
                                @else
                                    @if($product->qty <= 3)
                                        <p class="d-flex">
                                        <span
                                            class="d-inline-block right-side">{{ __('catalog::frontend.products.remaining_qty') }}</span>
                                            <span class="d-inline-block left-side">
                                                {{ $product->qty }}
                                        </span>
                                        </p>
                                    @endif
                                @endif
                            </div>

                        </div>

                        <div class="product-summ-price">

                            <div class="form-group">
                                <label>{{ __('catalog::frontend.products.notes') }}</label>
                                <textarea class="form-control"
                                          id="notes"
                                          name="notes"
                                          style="min-height: 100px !important;"
                                          placeholder="{{ __('catalog::frontend.products.notes') }}"
                                          rows="3">{{ getProductCartNotes($product, $variantPrd) }}</textarea>
                            </div>
                            <div id="responseMsg"></div>

                            <span class="price have-discount" id="priceSection">

                                @if(!is_null($variantPrd))
                                    @if($variantPrd->offer)
                                        <span
                                            class="price-before">{{ $variantPrd->price }} {{ __('apps::frontend.master.kwd') }}</span>
                                        {{ $variantPrd->offer->offer_price }} {{ __('apps::frontend.master.kwd') }}
                                    @else
                                        {{ $variantPrd->price }} {{ __('apps::frontend.master.kwd') }}
                                    @endif
                                @else
                                    @if($product->offer)
                                        @if(!is_null($product->offer->offer_price))
                                            <span
                                                class="price-before">{{ $product->price }} {{ __('apps::frontend.master.kwd') }}</span>
                                            {{ $product->offer->offer_price }} {{ __('apps::frontend.master.kwd') }}
                                        @else
                                            <span>{{ $product->price }} {{ __('apps::frontend.master.kwd') }}</span> /
                                            <span
                                                class="percentage-discount"> {{ $product->offer->percentage . ' %' }} {{ __('apps::frontend.master.discount') }} </span>
                                            {{--{{ calculateOfferAmountByPercentage($product->price, $product->offer->percentage) }} {{ __('apps::frontend.master.kwd') }}--}}
                                        @endif
                                    @else
                                        {{ $product->price }} {{ __('apps::frontend.master.kwd') }}
                                    @endif
                                @endif
                            </span>

                            <div id="successMsg"></div>

                            <div id="addVariantPrdToCartSection">
                                @if(is_null($product->variants) || count($product->variants) == 0)
                                    <form class="form"
                                          action="{{ route('frontend.shopping-cart.create-or-update', [ $product->translate(locale())->slug ]) }}"
                                          method="POST" data-id="{{ $product->id }}">
                                        @csrf

                                        <input type="hidden" id="productImage-{{ $product->id }}"
                                               value="{{ url($product->image) }}">
                                        <input type="hidden" id="productTitle-{{ $product->id }}"
                                               value="{{ $product->translate(locale())->title }}">
                                        <input type="hidden" id="productType"
                                               value="product">
                                        <input type="hidden" id="selectedOptions"
                                               value="">
                                        <input type="hidden" id="selectedOptionsValue"
                                               value="">

                                        <div class="align-items-center d-flex">
                                            <h5> {{ __('catalog::frontend.products.quantity') }} </h5>
                                            <div class="quantity">
                                                <div class="buttons-added">
                                                    <button class="sign plus"><i class="fa fa-chevron-up"></i></button>
                                                    <input type="text"
                                                           id="prodQuantity"
                                                           value="{{ getCartItemById($product->id) ? getCartItemById($product->id)->quantity : '1' }}"
                                                           title="Qty" class="input-text qty text" size="1">
                                                    <button class="sign minus"><i class="fa fa-chevron-down"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <button id="btnAddToCart" class="btn btn-them add-cart-btn"
                                                    type="button"><i class="ti-shopping-cart"></i>
                                                {{ __('catalog::frontend.products.add_to_cart') }}
                                            </button>

                                            <div id="loaderDiv">
                                                <div class="my-loader"></div>
                                            </div>

                                        </div>

                                    </form>
                                @elseif(!is_null($variantPrd))
                                    <form class="form"
                                          action="{{ route('frontend.shopping-cart.create-or-update', [ $product->translate(locale())->slug, $variantPrd->id ]) }}"
                                          method="POST" data-id="{{ $variantPrd->id }}">
                                        @csrf

                                        <input type="hidden" id="productImage-{{ $variantPrd->id }}"
                                               value="{{ url($variantPrd->image) }}">
                                        <input type="hidden" id="productTitle-{{ $variantPrd->id }}"
                                               value="{{ $product->translate(locale())->title }}">
                                        <input type="hidden" id="productType"
                                               value="variation">
                                        <input type="hidden" id="selectedOptions"
                                               value="{{ json_encode($selectedOptions) }}">
                                        <input type="hidden" id="selectedOptionsValue"
                                               value="{{ json_encode($selectedOptionsValue) }}">

                                        <div class="align-items-center d-flex">
                                            <h5> {{ __('catalog::frontend.products.quantity') }} </h5>
                                            <div class="quantity">
                                                <div class="buttons-added">
                                                    <button class="sign plus"><i class="fa fa-chevron-up"></i></button>
                                                    <input type="text"
                                                           id="prodQuantity"
                                                           value="{{ getCartItemById('var-'.$variantPrd->id) ? getCartItemById('var-'.$variantPrd->id)->quantity : '1' }}"
                                                           title="Qty" class="input-text qty text" size="1">
                                                    <button class="sign minus"><i class="fa fa-chevron-down"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <button id="btnAddToCart" class="btn btn-them add-cart-btn"
                                                    type="button"><i class="ti-shopping-cart"></i>
                                                {{ __('catalog::frontend.products.add_to_cart') }}
                                            </button>

                                            <div id="loaderDiv">
                                                <div class="my-loader"></div>
                                            </div>

                                        </div>

                                    </form>
                                @endif
                            </div>

                        </div>

                    </div>
                </div>
            </div>
            <div class="product-more-det mt-40">
                <ul class="nav nav-pills text-center mb-50">
                    <li>
                        <a class="active" data-toggle="pill"
                           href="#menu1">{{ __('catalog::frontend.products.product_description') }}</a>
                    </li>
                    <li>
                        <a class="" data-toggle="pill"
                           href="#menu2">{{ __('catalog::frontend.products.description') }}</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div id="menu1" class="tab-pane fade show active">
                        <p>
                            {!! $product->translate(locale())->short_description !!}
                        </p>
                    </div>
                    <div id="menu2" class="tab-pane fade show">
                        <p>
                            {!! $product->translate(locale())->description !!}
                        </p>
                    </div>
                </div>
            </div>

            @if(count($related_products) > 0)
                <div class="home-products mt-40 mb-0">
                    <h3 class="slider-title"> {{ __('catalog::frontend.products.related_products') }}</h3>

                    <div class="owl-carousel products-slider">

                        @foreach($related_products as $k => $prd)
                            <div class="product-grid">
                                <div class="product-image d-flex align-items-center">
                                    {{--<span class="pro-bdge new">جديد</span>--}}
                                    <a href="{{ route('frontend.products.index', $prd->translate(locale())->slug) }}">
                                        @if($prd->tags)
                                            @foreach($prd->tags as $i => $tag)
                                                <label class="label-tag-product"
                                                       style="background-color: {{ $tag->color ?? '#ccc' }}">{{ $tag->translate(locale())->title }}</label>
                                            @endforeach
                                        @endif
                                        <img class="pic-1" src="{{ url($prd->image) }}">
                                    </a>
                                    <ul class="social">
                                        <li>
                                            <a href="{{ route('frontend.products.index', $prd->translate(locale())->slug) }}"
                                               data-tip="{{ __('apps::frontend.products.details.title') }}">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                        </li>

                                        @if(auth()->check() && !in_array($prd->id, array_column(auth()->user()->favourites->toArray(), 'id')))
                                            <li>
                                                <form class="favourites-form" method="POST">
                                                    @csrf
                                                    <a href="javascript:;"
                                                       onclick="generalAddToFavourites('{{ route('frontend.profile.favourites.store', [ $prd->id ]) }}', '{{ $prd->id }}')"
                                                       id="btnAddToFavourites-{{ $prd->id }}"
                                                       data-tip="{{ __('apps::frontend.products.add_to_favourite') }}">
                                                        <i class="ti-heart"></i>
                                                    </a>
                                                </form>
                                            </li>
                                        @endif

                                        @if(is_null($prd->variants) || count($prd->variants) == 0)
                                            <li>

                                                <div class="generalLoaderDiv" id="generalLoaderDiv-{{ $prd->id }}">
                                                    <div class="my-loader"></div>
                                                </div>

                                                <form class="general-form" method="POST">
                                                    @csrf

                                                    <input type="hidden" id="productImage-{{ $prd->id }}"
                                                           value="{{ url($prd->image) }}">
                                                    <input type="hidden" id="productTitle-{{ $prd->id }}"
                                                           value="{{ $prd->translate(locale())->title }}">
                                                    <input type="hidden" id="productQuantity-{{ $prd->id }}"
                                                           value="{{ getCartItemById($prd->id) ? getCartItemById($prd->id)->quantity + 1 : 1 }}">

                                                    <a href="javascript:;" id="general_add_to_cart-{{ $prd->id }}"
                                                       class="btnGeneralAddToCart"
                                                       onclick="generalAddToCart('{{ route("frontend.shopping-cart.create-or-update", [ $prd->translate(locale())->slug ]) }}', '{{ $prd->id }}')"
                                                       data-tip="{{ __('apps::frontend.products.add_to_cart') }}">
                                                        <i class="ti-shopping-cart-full"></i>
                                                    </a>

                                                </form>

                                            </li>
                                        @endif

                                    </ul>

                                </div>
                                <div class="product-content">
                                    <h3 class="title">
                                        <a href="{{ route('frontend.products.index', $prd->translate(locale())->slug) }}">{{ $prd->translate(locale())->title }}</a>
                                    </h3>
                                    <span class="price have-discount">
                                        @if($prd->offer)
                                            @if(!is_null($prd->offer->offer_price))
                                                <span
                                                    class="price-before">{{ $prd->price }} {{ __('apps::frontend.master.kwd') }}</span>
                                                {{ $prd->offer->offer_price }} {{ __('apps::frontend.master.kwd') }}
                                            @else
                                                <span>{{ $prd->price }} {{ __('apps::frontend.master.kwd') }}</span>
                                                /
                                                <span
                                                    class="percentage-discount"> {{ $prd->offer->percentage . ' %' }} {{ __('apps::frontend.master.discount') }} </span>
                                                {{--{{ calculateOfferAmountByPercentage($prd->price, $prd->offer->percentage) }} {{ __('apps::frontend.master.kwd') }}--}}
                                            @endif
                                        @else
                                            {{ $prd->price }} {{ __('apps::frontend.master.kwd') }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            @endif

        </div>
    </div>

@endsection

@section('externalJs')

    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <script>

        $(document).on('click', '#btnAddToCart', function (e) {

            var token = $(this).closest('.form').find('input[name="_token"]').val();
            var action = $(this).closest('.form').attr('action');
            var qty = $('#prodQuantity').val();
            var notes = $('#notes').val();

            var productId = $(this).closest('.form').attr('data-id');
            var productType = $(this).closest('.form').find('#productType').val();
            var productImage = $(this).closest('.form').find('#productImage-' + productId).val();
            var productTitle = $(this).closest('.form').find('#productTitle-' + productId).val();
            var selectedOptions = $(this).closest('.form').find('#selectedOptions').val();
            var selectedOptionsValue = $(this).closest('.form').find('#selectedOptionsValue').val();

            e.preventDefault();

            if (parseInt(qty) > 0) {

                $(this).hide();
                $('#loaderDiv').show();

                $.ajax({
                    method: "POST",
                    url: action,
                    data: {
                        "qty": qty,
                        "request_type": 'product',
                        "product_type": productType,
                        "selectedOptions": selectedOptions,
                        "selectedOptionsValue": selectedOptionsValue,
                        "notes": notes ?? null,
                        "_token": token,
                    },
                    beforeSend: function () {
                    },
                    success: function (data) {
                        var params = {
                            'productId': productId,
                            'productImage': productImage,
                            'productTitle': data.data.productTitle,
                            'productQuantity': data.data.productQuantity,
                            'productPrice': data.data.productPrice,
                            'productDetailsRoute': data.data.productDetailsRoute,
                            'cartCount': data.data.cartCount,
                            'cartSubTotal': data.data.subTotal,
                            'product_type': productType,
                        };

                        updateHeaderCart(params);

                        if (data.data.remainingQty <= 3) {
                            var qty = `
                                <p class="d-flex">
                                    <span class="d-inline-block right-side">{{ __('catalog::frontend.products.remaining_qty') }}</span>
                                    <span class="d-inline-block left-side">${data.data.remainingQty}</span>
                                </p>
                            `;
                            $('#remainingQtySection').html(qty);
                        }

                        var msg = `
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                ${data.message}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        `;
                        $('#successMsg').html(msg);
                    },
                    error: function (data) {
                        $('#loaderDiv').hide();
                        $('#btnAddToCart').show();
                        // displayErrorsMsg(data);

                        let getJSON = $.parseJSON(data.responseText);
                        let error = '';
                        if (getJSON.errors['notes'])
                            error = getJSON.errors['notes'];
                        else
                            error = getJSON.errors;

                        let msg = `
                            <div class="alert alert-danger alert-dismissible" role="alert">
                                ${error}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        `;
                        $('#responseMsg').html(msg);
                    },
                    complete: function (data) {
                        $('#loaderDiv').hide();
                        $('#btnAddToCart').show();
                    },
                });
            }

        });

        function getVariationInfo(e, productId) {

            var selectedOptions = [];
            var selectedOptionsValue = [];

            $('#remainingQtySection').empty();
            $('.product-var-options').each(function (i, item) {
                selectedOpt = $(this).attr('data-option-id');
                selectedOptions.push(selectedOpt);
                selectedOptionsValue.push($(this).val());
            });

            if (selectedOptions.length != 0 && !selectedOptionsValue.includes(undefined) && !selectedOptionsValue.includes("")) {
                $.ajax({
                    method: "GET",
                    url: '{{ route('frontend.get_prd_variation_info') }}',
                    data: {
                        "selectedOptions": selectedOptions,
                        "selectedOptionsValue": selectedOptionsValue,
                        "product_id": productId,
                        "_token": '{{ csrf_token() }}',
                    },
                    beforeSend: function () {
                    },
                    success: function (data) {
                        // console.log('data::', data);

                        var variantProduct = data.data.variantProduct;

                        if (variantProduct.sku) {
                            var sku = `
                                <p class="d-flex">
                                    <span class="d-inline-block right-side">{{ __('catalog::frontend.products.sku') }}</span>
                                    <span class="d-inline-block left-side">${variantProduct.sku}</span>
                                </p>
                            `;
                            $('#skuSection').html(sku);
                        }

                        if (variantProduct.qty <= 3) {
                            var qty = `
                                <p class="d-flex">
                                    <span class="d-inline-block right-side">{{ __('catalog::frontend.products.remaining_qty') }}</span>
                                    <span class="d-inline-block left-side">${variantProduct.qty}</span>
                                </p>
                            `;
                            $('#remainingQtySection').html(qty);
                        }

                        if (variantProduct.price) {
                            if (variantProduct.offer) {
                                var price = `
                                <span class="price-before">${variantProduct.price} {{ __('apps::frontend.master.kwd') }}</span>
                                ${variantProduct.offer.offer_price} {{ __('apps::frontend.master.kwd') }}
                                `;
                            } else {
                                var price = `${variantProduct.price} {{ __('apps::frontend.master.kwd') }}`;
                            }
                            $('#priceSection').html(price);
                        }

                        if (variantProduct.image) {
                            var selectedImg = `
                            <div class="sp-large" style="overflow: hidden; height: auto; width: auto;">
                                <a href="${variantProduct.image}" class="sp-current-big">
                                    <img src="${variantProduct.image}" alt="">
                                </a>
                            </div>
                            `;
                            $('.sp-large').remove();
                            $('#mainProductSlider').prepend(selectedImg);
                        }

                    },
                    error: function (data) {
                        displayErrorsMsg(data);
                    },
                    complete: function (data) {
                        // console.log('data::', data);
                        var getJSON = $.parseJSON(data.responseText);
                        // console.log('getJSON::', getJSON);

                        $('#addVariantPrdToCartSection').html(getJSON.data.form_view);
                    },
                });
            } else {
                $('#addVariantPrdToCartSection').empty();
            }

        }

        @if(!is_null($variantPrd) && !empty($variantPrd->image) && $variantPrd->id == request()->var)
        $(document).ready(function () {
            var img = `
                <div class="sp-large">
                    <a href="{{ $variantPrd->image }}"
                       class="sp-current-big">
                        <img src="{{ $variantPrd->image }}" alt="">
                    </a>
                </div>
            `;
            $('.sp-large').remove();
            $('#mainProductSlider').prepend(img);
        });
        @endif

    </script>

@endsection
