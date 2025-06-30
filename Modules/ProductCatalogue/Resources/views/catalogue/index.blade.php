@extends('layouts.guest')
@section('title', $business->name)

@section('content')

<style>
  
.carousel-container {
  overflow: hidden;
  max-height: 500px;
  position: relative;
  margin: auto;
  z-index: 0;
}

/* Hide the images by default */
.mySlides {
  display: none;
}
.mySlides img {
  display: block;
  width: 100%;
  max-height: 533px
}



/* Next & previous buttons */
.prev,
.next {
  cursor: pointer;
  position: absolute;
  top: 50%;
  transform: translate(0, -50%);
  width: auto;
  padding: 20px;
  color: white;
  font-weight: bold;
  font-size: 24px;
  border-radius: 0 8px 8px 0;
  background: rgba(173, 216, 230, 0.1);
  user-select: none;
}
.next {
  right: 0;
  border-radius: 8px 0 0 8px;
}
.prev:hover,
.next:hover {
  background-color: rgba(173, 216, 230, 0.3);
}


.active,
.dots:hover {
  background-color: rgba(173, 216, 230, 0.8);
}

/* transition animation */
.animate {
  -webkit-animation-name: animate;
  -webkit-animation-duration: 1s;
  animation-name: animate;
  animation-duration: 4s;
}

@keyframes animate {
    from {
        transform: scale(1);
    }
    to {
        transform: scale(1.1);
    }
}
.dp-pd{
    padding-right: 29px;
    padding-left: 82px;
}

.product-fix {
      position: fixed;
      bottom: 20px;
      color: #FFF;
      font-size: 18px;
      background-color: #325e51;
      box-shadow: 2px 2px 3px #999;
      z-index: 100;
      display: flex;
      justify-content: space-between;
      width: 100%;
      padding: 10px;
      height: 56px;
  }

  
  .square,
  .square-with-number,
  .price {
      width: 30%;
      text-align: center;
  }
  
  .square,
  .square-with-number {
      border-radius: 10px; 
      box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.3); 
  }
        .search-container {
            position: relative;
            display: inline-block;
        }

        .search-input {
            width: 300px;
            padding: 10px 40px 10px 10px;
            border: 1px solid #ccc;
            border-radius: 25px;
            outline: none;
        }

        .search-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
            cursor: pointer;
        }

</style>
<!-- Content Header (Page header) -->
<section class="content-header text-center" id="top">
    <h2>{{$business->name}}</h2>
    <h4 class="mb-0">{{$business_location->name}}</h4>
    <p>{!! $business_location->location_address !!}</p>
</section>

<section class="mt-more">
    <!-- Full-width images with number and caption text -->
<div class="carousel-container">
    @foreach($banners as $banner)
    <div class="mySlides animate">
        <a href="{{ $banner->cta }}">
      <img src="{{ asset($banner->image) }}" alt="slide" />
    </a>
    </div>
    @endforeach
    <!-- Next and previous buttons -->
    <a class="prev" onclick="prevSlide()">&#10094;</a>
    <a class="next" onclick="nextSlide()">&#10095;</a>
  
    <!-- The dots/circles -->
  </div>
</section>



<section class="no-print mt-5">
    <div class="container">

        
        <!-- Static navbar -->
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand menu" href="#top">
                        @if(!empty($business->logo))
                            <img src="{{asset( 'uploads/business_logos/' . $business->logo)}}" alt="Logo" width="30">
                        @else
                            <i class="fas fa-boxes"></i>
                        @endif
                    </a>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                    @foreach($categories as $key => $value)
                        <li><a href="#category{{$key}}" class="menu">{{$value}}</a></li>
                    @endforeach 
                        <li><a href="#category0" class="menu">Uncategorized</a></li>  

                    </ul>
                </div><!--/.nav-collapse -->
            </div><!--/.container-fluid -->
        </nav>
    </div> <!-- /container -->
</section>
<!-- Main content -->

<section class="content pt-0">
    <div class="container">

        <div style="text-align: center; position: relative;">
            <a href="{{ route('view-cart', ['business_id' => $business->id, 'location_id' => $business_location->id]) }}" class="btn btn-primary">
                View Cart 
                <span id="cart-item-count" style="position: absolute; background-color: red; color: white; font-size: 12px; padding: 2px 8px; top: -7px; border-radius: 50%;">
                    {{ $totalItems }}
                </span>
            </a>
            
        </div>
        
        <div class="search-container" style="margin-top: 12px">
            <input type="text" class="search-input" id="search_product" placeholder="Search...">
            <i class="fas fa-search search-icon"></i>
        </div>
        @foreach($products as $product_category)
        <div class="row product-category" id="category{{ $product_category->first()->category->id ?? 0 }}" data-products='@json($product_category->pluck("name"))'>
            <div class="col-md-12">
                    <h2 class="page-header" id="category{{$product_category->first()->category->id ?? 0}}">{{$product_category->first()->category->name ?? 'Uncategorized'}}</h2>
                </div>
            </div>
            <div class="row eq-height-row">
                @foreach($product_category as $product)
                <div class="col-md-3 eq-height-col col-xs-12 product" id="product-{{ $product->id }}">
                    <div class="box box-solid product-box">
                        <div class="box-body">
                            <a href="#" class="show-product-details" data-href="{{action([\Modules\ProductCatalogue\Http\Controllers\ProductCatalogueController::class, 'show'],  [$business->id, $product->id])}}?location_id={{$business_location->id}}">
                            <img src="{{$product->image_url}}" class="img-responsive catalogue"></a>
            
                            @php
                                $discount = $discounts->firstWhere('brand_id', $product->brand_id);
                                if(empty($discount)){
                                    $discount = $discounts->firstWhere('category_id', $product->category_id);
                                }
                            @endphp
            
                            @if(!empty($discount))
                                <span class="label label-warning discount-badge">- {{($discount->discount_amount)}}%</span>
                            @endif
            
                            @php
                                $max_price = $product->variations->max('sell_price_inc_tax');
                                $min_price = $product->variations->min('sell_price_inc_tax');
                            @endphp
                            <h2 class="catalogue-title">
                                <a href="#" class="show-product-details" data-href="{{ action([\Modules\ProductCatalogue\Http\Controllers\ProductCatalogueController::class, 'show'], [$business->id, $product->id]) }}?location_id={{ $business_location->id }}">
                                    <span class="product-title" data-name="{{ strtolower($product->name) }}">{{ $product->name }}</span>
                                </a>
                            </h2>
                            <table class="table no-border product-info-table">
                                <tr>
                                    <th class="pb-0"> @lang('lang_v1.price'):</th>
                                    <td class="pb-0">
                                        <span class="display_currency" data-currency_symbol="true">{{($max_price)}}</span> @if($max_price != $min_price) - <span class="display_currency" data-currency_symbol="true">{{($min_price)}}</span> @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="pb-0"> @lang('product.sku'):</th>
                                    <td class="pb-0">{{$product->sku}}</td>
                                </tr>
                                @if($product->type == 'variable')
                                    @php
                                        $variations = $product->variations->groupBy('product_variation_id');
                                    @endphp
                                    @foreach($variations as $product_variation)
                                        <tr>
                                            <th>{{$product_variation->first()->product_variation->name}}:</th>
                                            <td>
                                                <select class="form-control input-sm variation-select">
                                                @foreach($product_variation as $variation)
                                                    <option value="{{$variation->id}}">{{$variation->name}} ({{$variation->sub_sku}}) - {{($variation->sell_price_inc_tax)}}</option>
                                                @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <input type="hidden" class="single-variation-id" value="{{ $product->variations->first()->id }}">
                                @endif
                            </table>
                            <button class="btn btn-primary btn-block add-to-cart" data-product-id="{{ $product->id }}" data-product-type="{{ $product->type }}">
                                <div class="loading-spinner" style="display: none;">
                                    <i class="fa fa-spinner fa-spin"></i>
                                </div>
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
                @if($loop->iteration%4 == 0)
                    <div class="clearfix"></div>
                @endif
            @endforeach
            
            
            </div>
        @endforeach
    </div>
    <div class='scrolltop no-print'>
        <div class='scroll icon'><i class="fas fa-angle-up"></i></div>
    </div>
</section>
<!-- /.content -->
<!-- Add currency related field-->
<input type="hidden" id="__code" value="{{$business->currency->code}}">
<input type="hidden" id="__symbol" value="{{$business->currency->symbol}}">
<input type="hidden" id="__thousand" value="{{$business->currency->thousand_separator}}">
<input type="hidden" id="__decimal" value="{{$business->currency->decimal_separator}}">
<input type="hidden" id="__symbol_placement" value="{{$business->currency->currency_symbol_placement}}">
<input type="hidden" id="__precision" value="{{$business->currency_precision}}">
<input type="hidden" id="__quantity_precision" value="{{$business->quantity_precision}}">
<div class="modal fade product_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>
@stop
@section('javascript')
<script type="text/javascript">

    (function($) {
    $(document).ready( function() {
        //Set global currency to be used in the application
        __currency_symbol = $('input#__symbol').val();
        __currency_thousand_separator = $('input#__thousand').val();
        __currency_decimal_separator = $('input#__decimal').val();
        __currency_symbol_placement = $('input#__symbol_placement').val();
        if ($('input#__precision').length > 0) {
            __currency_precision = $('input#__precision').val();
        } else {
            __currency_precision = 2;
        }

        if ($('input#__quantity_precision').length > 0) {
            __quantity_precision = $('input#__quantity_precision').val();
        } else {
            __quantity_precision = 2;
        }

        //Set page level currency to be used for some pages. (Purchase page)
        if ($('input#p_symbol').length > 0) {
            __p_currency_symbol = $('input#p_symbol').val();
            __p_currency_thousand_separator = $('input#p_thousand').val();
            __p_currency_decimal_separator = $('input#p_decimal').val();
        }

        __currency_convert_recursively($('.content'));
    });

    $(document).on('click', '.show-product-details', function(e){
        e.preventDefault();
        $.ajax({
            url: $(this).data('href'),
            dataType: 'html',
            success: function(result) {
                $('.product_modal')
                    .html(result)
                    .modal('show');
                __currency_convert_recursively($('.product_modal'));
            },
        });
    });

    $(document).on('click', '.menu', function(e){
        e.preventDefault();
        $('.navbar-toggle').addClass('collapsed');
        $('.navbar-collapse').removeClass('in');

        var cat_id = $(this).attr('href');
        if ($(cat_id).length) {
            $('html, body').animate({
                scrollTop: $(cat_id).offset().top
            }, 1000);
        }
    });

    })(jQuery);

    $(window).scroll(function() {
        var height = $(window).scrollTop();

        if(height  > 180) {
            $('nav').addClass('navbar-fixed-top');
            $('.scrolltop:hidden').stop(true, true).fadeIn();
        } else {
            $('nav').removeClass('navbar-fixed-top');
            $('.scrolltop').stop(true, true).fadeOut();
        }
    });

    $(document).on('click', '.scroll', function(e){
        $("html,body").animate({scrollTop:$("#top").offset().top},"1000");
        return false;
    });

    
</script>

<script>
$(document).ready(function() {

    $("#search_product").on('keyup', function() {
        const search = $(this).val().toLowerCase();
        
        if (!search.length) {
            $(".product-category, .product").show();
        } else {
            $(".product-category, .product").hide();
            $(".product-title").each(function() {
                const productName = $(this).data('name');
                if (productName.includes(search)) {
                    $(this).closest('.product').show();
                    $(this).closest('.product-container').prev('.product-category').show();
                }
            });
        }
    });

    $('.add-to-cart').on('click', function(e) {
    console.log('workd');
    e.preventDefault();

    var productId = $(this).data('product-id') || null;
    var productName = $(this).closest('.product-box').find('.catalogue-title').text().trim(); 
    var productType = $(this).data('product-type');
    var productImageUrl = $(this).closest('.product-box').find('.catalogue').attr('src');
    var variationId;
    var productWithVariationName = productName; 

    if (productType === 'variable') {
        variationId = $(this).closest('.product-box').find('.variation-select').val();
        var variationName = $(this).closest('.product-box').find('.variation-select option:selected').text(); 
        productWithVariationName += ' - ' + variationName; 
    } else {
        variationId = $(this).closest('.product-box').find('.single-variation-id').val();
    }

    if (!variationId) {
        alert('Please select a variation.');
        return;
    }

    var businessId = {{ $business->id }};
    var locationId = {{ $business_location->id }};
    var $this = $(this);

    var $productImage = $this.closest('.add-to-cart');
    var $loadingSpinner = $productImage.find('.loading-spinner');
    $loadingSpinner.show();

    $.ajax({
        url: "/add-to-cart",
        method: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            product_id: productId,
            name: productWithVariationName,
            image:productImageUrl,
            variation_id: variationId,
            business_id: businessId,
            location_id: locationId,
        },
        success: function(response) {
            $loadingSpinner.hide();
            toastr.success(response.message);
            $('#cart-item-count').text('');
            $('#cart-item-count').text(response.totalItems);

        },
        error: function(response) {
            alert('Error adding to cart');
            $loadingSpinner.hide();
        }
    });
});

});


</script>


<script>
    let slideIndex = 0;
let autoSlideInterval;

function showSlides() {
    let slides = document.querySelectorAll(".mySlides");

    console.log("Total slides: ", slides.length);

    if (slides.length === 0) {
        console.error("No slides found");
        return;
    }

    if (slideIndex >= slides.length) {
        slideIndex = 0;
    }
    if (slideIndex < 0) {
        slideIndex = slides.length - 1;
    }

    slides.forEach((slide) => {
        slide.style.display = "none";
    });

    slides[slideIndex].style.display = "block";

}

function nextSlide() {
    slideIndex++;
    showSlides();
    resetAutoSlide();
}

function prevSlide() {
    slideIndex--;
    showSlides();
    resetAutoSlide();
}

function startAutoSlide() {
    let slides = document.querySelectorAll(".mySlides");
    if (slides.length > 1) {
        autoSlideInterval = setInterval(nextSlide, 5000);
    }
}

function resetAutoSlide() {
    clearInterval(autoSlideInterval);
    startAutoSlide();
}

window.onload = function () {
    showSlides();
    startAutoSlide();
};

</script>
@endsection
