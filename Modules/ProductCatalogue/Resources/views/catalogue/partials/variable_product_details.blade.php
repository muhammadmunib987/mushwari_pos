<div class="row">
	<div class="col-md-12">
		<h4>@lang('product.variations'):</h4>
	</div>
	<div class="col-md-12 product-box">
		@foreach($product->variations as $variation)
			<div class="col-md-3 mb-12">
				<div class="attachment-block clearfix">
	                @if(!empty($variation->media->first()))
						<img src="{{$variation->media->first()->display_url}}" class="attachment-img" alt="Product image">
					@else
						<img src="{{$product->image_url}}" alt="Product image" class="attachment-img">
					@endif
					@if(!empty($discounts[$variation->id]))
      					<span class="label label-warning discount-badge-small">- {{@num_format($discounts[$variation->id]->discount_amount)}}%</span>
      				@endif
	                <div class="attachment-pushed">
	                  <h4 class="attachment-heading">{{$variation->product_variation->name}} - {{ $variation->name }}</h4>
					  <input type="hidden" class="variation_name" name="variation_name" value="{{ $product->name }} - {{ $variation->product_variation->name }} - {{ $variation->name }} 2">
                        <input type="hidden" name="varid" value="{{ $variation->id }}">
					  
	                  <div class="attachment-text">
	                  	<br>
	                    <table class="table table-slim no-border">
							<tr>
								<th>@lang('product.sku'):</th>
			      				<td>{{$variation->sub_sku }}</td>
							</tr>
							<tr>
								<th>@lang('lang_v1.price'):</th>
			      				<td><span class="display_currency" data-currency_symbol="true">{{ $variation->sell_price_inc_tax }}</span></td>
								<td>
									<button class="btn btn-primary mt-5 btn-block add-to-cart" data-variation-id="{{ $variation->id }}" data-product-id="{{ $product->id }}" data-product-type="{{ $product->type }}">
										<div class="loading-spinner" style="display: none;">
											<i class="fa fa-spinner fa-spin"></i>
										</div>
										Add to Cart
									</button>
								</td>
							</tr>
						</table>
	                  </div>
	                </div>
	              </div>
			</div>
		@endforeach
	</div>
</div>

<script>
	$('.add-to-cart').on('click', function(e) {
    console.log('workd');
    e.preventDefault();

    var productId = $(this).data('product-id') || null;
    var productName = $('.variation_name').val();
    var variationId = $(this).data('variation-id') || null;
    var productType = $(this).data('product-type');
    var productImageUrl = $(this).closest('.product-box').find('.attachment-img').attr('src');
    var variationId;
    var productWithVariationName = productName; 


    if (!variationId) {
        alert('Please select a variation.');
        return;
    }

    var businessId = {{ $product->business_id }};
    var locationId = {{ $location_id }};
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
			setTimeout(function() {
                location.reload();
            }, 2000);
			$('#cart-item-count').text(response.totalItems);
        },
        error: function(response) {
            alert('Error adding to cart');
            $loadingSpinner.hide();
        }
    });
});


</script>