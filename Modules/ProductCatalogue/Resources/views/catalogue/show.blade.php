<div class="modal-dialog modal-xl" role="document">
	<div class="modal-content product-box">
		<div class="modal-header">
		    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		      <h4 class="modal-title" id="modalTitle">{{$product->name}}</h4>
	    </div>
	    <div class="modal-body">
      		<div class="row">
      			<div class="col-md-4">
      				<div class="thumbnail">
      					<img src="{{$product->image_url}}" class="catalogue" alt="Product image">
      					@if($product->type == 'single' && !empty($discounts[$product->variations->first()->id]))
      						<span class="label label-warning discount-badge">- {{@num_format($discounts[$product->variations->first()->id]->discount_amount)}}%</span>
      					@endif
      				</div>
      			</div>
				  <input type="hidden" class="single-variation-id" value="{{ $product->variations->first()->id }}">

      			<div class="col-md-8">
      				@if($product->type == 'single' || $product->type == 'combo')
      					<div class="col-md-12">
      						<p class="lead">@lang('lang_v1.price'): &nbsp;&nbsp;&nbsp;<span class="display_currency" data-currency_symbol="true">{{ $product->variations->first()->sell_price_inc_tax }}</span></p><br>
      					</div>
      				@endif
      				<div class="col-md-12">
	      				<table class="table no-border table-slim">
	      					<tr>
	      						<th>@lang('product.sku'):</th>
	      						<td>{{$product->sku }}</td>
	      					</tr>
	      					<tr>
	      						<th>@lang('product.category'):</th>
	      						<td>{{$product->category->name ?? '--' }}</td>
	      					</tr>
	      					<tr>
	      						<th>@lang('product.sub_category'):</th>
	      						<td>{{$product->sub_category->name ?? '--' }}</td>
	      					</tr>
	      					<tr>
	      						<th>@lang('product.brand'):</th>
	      						<td>{{$product->brand->name ?? '--' }}</td>
	      					</tr>
							  @if($product->type == 'single')
							<tr>
								<th>
									<button class="btn btn-primary mt-5 btn-block add-to-cart" data-product-id="{{ $product->id }}" data-product-type="{{ $product->type }}">
										<div class="loading-spinner" style="display: none;">
											<i class="fa fa-spinner fa-spin"></i>
										</div>
										Add to Cart
									</button>
								</th>
							</tr>
							@endif
	      					@php 
	    						$custom_labels = json_decode(session('business.custom_labels'), true);
							@endphp
							@if(!empty($product->product_custom_field1))
								<tr>
	      							<th>{{ $custom_labels['product']['custom_field_1'] ?? __('lang_v1.product_custom_field1') }}: </th>
									<td>{{$product->product_custom_field1 }}</td>
								</tr>
							@endif

							@if(!empty($product->product_custom_field2))
								<tr>
		      						<th>{{ $custom_labels['product']['custom_field_2'] ?? __('lang_v1.product_custom_field2') }}: </th>
									<td>{{$product->product_custom_field2 }}</td>
								</tr>
							@endif

							@if(!empty($product->product_custom_field3))
								<tr>
	      							<th>{{ $custom_labels['product']['custom_field_3'] ?? __('lang_v1.product_custom_field3') }}: </th>
									<td>{{$product->product_custom_field3 }}</td>
								</tr>
							@endif

							@if(!empty($product->product_custom_field4))
								<tr>
	      							<th>{{ $custom_labels['product']['custom_field_4'] ?? __('lang_v1.product_custom_field4') }}: </th>
									<td>{{$product->product_custom_field4 }}</td>
								</tr>
							@endif
	      					<tr>
	      						<td colspan="2"><br><br>{!! $product->product_description !!}</td>
	      					</tr>
	      				</table>
      				</div>
	      		</div>
      		</div>
      		@if($product->type == 'variable')
      			@include('productcatalogue::catalogue.partials.variable_product_details')
      		@elseif($product->type == 'combo')
      			@include('productcatalogue::catalogue.partials.combo_product_details')
      		@endif
      	</div>
      	<div class="modal-footer">
	      	<button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close' )</button>
	    </div>
	</div>
</div>
<script>
	@if($product->type == 'single')
	$('.add-to-cart').on('click', function(e) {
    console.log('workd');
    e.preventDefault();

    var productId = $(this).data('product-id') || null;
    var productName = $(this).closest('.product-box').find('.modal-title').text().trim(); 
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
        },
        error: function(response) {
            alert('Error adding to cart');
            $loadingSpinner.hide();
			$('#cart-item-count').text(response.totalItems);
        }
    });
});

@endif

</script>