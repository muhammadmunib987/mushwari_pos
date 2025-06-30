<table align="center" style="border-spacing: {{ $barcode_details->col_distance }}in {{ $barcode_details->row_distance }}in;">
@foreach($page_products as $page_product)

    @if($loop->index % $barcode_details->stickers_in_one_row == 0)
        <tr>
    @endif

    <td align="center" valign="middle">
        <div style="width: 2.6in; height: 1.6in; padding: 0.1in; font-family: Arial, sans-serif; position: relative; border: 1px dotted lightgray; box-sizing: border-box; display: flex;">

            {{-- Left Section (70%) --}}
            <div style="width: 70%; display: flex; flex-direction: column; justify-content: space-between;">
                {{-- Business Name --}}
                <div style="font-size: 10px; font-weight: bold; margin-bottom: 1px;">
                    @if(!empty($print['business_name']))
                        {{ $business_name }}
                    @endif
                </div>

                {{-- Packing Date --}}
                <div style="font-size: 8px; margin-bottom: 2px;">
                    {{ $page_product->packing_date }}
                </div>

                {{-- Barcode (SVG) --}}
                <div style="text-align: center; margin: 2px 0;">
                    <div style="width: 100%; height: auto;">
                        {!! DNS1D::getBarcodeSVG($page_product->sub_sku, 'C128', 3, 80, 'black') !!}
                    </div>
                </div>

                {{-- Barcode Text --}}
                <div style="text-align: center; font-size: 8px; margin-bottom: 1px;">
                    {{ $page_product->sub_sku }} ddd
                </div>

                {{-- Product Name + Color --}}
                @php
                    $variationParts = explode(' ', $page_product->variation_name ?? '');
                    $colorValue = $variationParts[0] ?? '';
                @endphp
                <div style="font-size: 9px; font-weight: bold; white-space: nowrap; overflow: hidden;">
                     {{ Str::limit($page_product->product_actual_name, 20) }} ({{ $colorValue }}) 
                </div>
            </div>

            {{-- Right Section (30%) --}}
            <div style="width: 30%; display: flex; flex-direction: column; justify-content: space-between; align-items: flex-end;">
                {{-- Price --}}
                <div style="font-size: 9px; font-weight: bold; text-align: right;">
                    Price:
                    @if($print['price_type'] == 'inclusive')
                        {{ @num_format($page_product->sell_price_inc_tax) }}
                    @else
                        {{ @num_format($page_product->default_sell_price) }}
                    @endif
                </div>

                {{-- Size --}}
                @php
                    $sizeValue = $variationParts[1] ?? '';
                @endphp
                <div style="font-size: 22px; font-weight: bold; line-height: 1;">
                    {{ $sizeValue }}
                </div>

                {{-- Type / Category --}}
                <div style="font-size: 8px; text-align: right;">
                    {{ $page_product->category_name ?? 'N/A' }}
                </div>
            </div>

        </div>
    </td>

    @if($loop->iteration % $barcode_details->stickers_in_one_row == 0)
        </tr>
    @endif

@endforeach
</table>

<style type="text/css">
    td {
        border: 1px dotted lightgray;
        padding: 0 !important;
    }

    @media print {
        table {
            page-break-after: always;
        }

        svg {
            width: 100% !important;
            height: auto !important;
            max-height: 0.7in;
        }

        svg path {
            shape-rendering: crispEdges;
        }

        img, svg {
            image-rendering: optimizeQuality;
            break-inside: avoid;
        }

        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        @page {
            size: {{ $paper_width }}in {{ $paper_height }}in;
            margin-top: {{ $margin_top }}in;
            margin-bottom: {{ $margin_top }}in;
            margin-left: {{ $margin_left }}in;
            margin-right: {{ $margin_left }}in;
        }
    }
</style>
