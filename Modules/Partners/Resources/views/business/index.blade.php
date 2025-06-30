@extends('layouts.app')
@section('title','Partners')

@section('content')
    <style>
        .table-striped th{
            background-color: #626161;
            color: #ffffff;
        }
    </style>

    @include('partners::layouts.nav')

    <section class="content-header">
        <h1>Companies Financial Estimate</h1>
    </section>

    <section class="content">
        @component('components.widget', ['class' => 'box-primary', 'title' =>''])
            @can('assets.create')
                @slot('tool')
                         <div class="row">
                         <div class="col-md-12">
                                @component('components.widget', ['class' => 'box-solid'])
                                    <table class="table no-border">
                                        <tr>
                                            <td>@lang('report.closing_stock') (@lang('lang_v1.by_purchase_price'))</td>
                                            <td>@lang('report.closing_stock') (@lang('lang_v1.by_sale_price'))</td>
                                            <td>@lang('lang_v1.potential_profit')</td>
                                            <td>@lang('lang_v1.profit_margin')</td>
                                        </tr>
                                        <tr>
                                            <td><h3  class="mb-0 mt-0">{{ number_format($closing_stock_by_pp,2)}}</h3> </td>
                                            <td><h3  class="mb-0 mt-0">{{ number_format($closing_stock_by_sp,2)}}</h3> </td>
                                            <td><h3  class="mb-0 mt-0">{{ number_format($potential_profit,2)}}</h3> </td>
                                            <td><h3  class="mb-0 mt-0">{{ number_format($profit_margin,2)}}</h3> </td>
                                        </tr>
                                    </table>
                                @endcomponent
                               {{-- @component('components.widget', ['class' => 'box-solid'])
                                        <table class="table no-border">
                                            <tr>
                                                <td>Total Purchases</td>
                                                <td>Total Purchase Return</td>
                                                <td>Total Payments</td>
                                                <td>Total Returns</td>

                                            </tr>
                                            <tr>
                                                <td><h3  class="mb-0 mt-0">{{ number_format($total_purchase,2)}}</h3> </td>
                                                <td><h3  class="mb-0 mt-0">{{ number_format($purchase_return,2)}}</h3> </td>
                                                <td><h3  class="mb-0 mt-0">{{ number_format($purchase_paid,2)}}</h3> </td>
                                                <td><h3  class="mb-0 mt-0">{{ number_format($purchase_return_paid,2)}}</h3> </td>

                                            </tr>
                                        </table>
                                    @endcomponent

                                @component('components.widget', ['class' => 'box-solid'])
                                        <table class="table no-border">
                                            <tr>
                                                <td>Total Sales</td>
                                                <td>Total Sale Return</td>
                                                <td>Total Payments</td>
                                                <td>Total Returns</td>

                                            </tr>
                                            <tr>
                                                <td><h3  class="mb-0 mt-0">{{ number_format($total_invoice,2)}}</h3> </td>
                                                <td><h3  class="mb-0 mt-0">{{ number_format($total_sell_return,2)}}</h3> </td>
                                                <td><h3  class="mb-0 mt-0">{{ number_format($invoice_received,2)}}</h3> </td>
                                                <td><h3  class="mb-0 mt-0">{{ number_format($invoice_received,2)}}</h3> </td>

                                            </tr>
                                        </table>
                                    @endcomponent--}}
                                @component('components.widget', ['class' => 'box-solid'])
                                    <table class="table no-border">
                                        <tr>
                                            <td>Fixed Assets</td>
                                            <td>Customer Dues</td>
                                            <td>Supplier Dues</td>

                                        </tr>
                                        <tr>
                                            <td><h3  class="mb-0 mt-0">{{ number_format($partnerassets,2)}}</h3> </td>
                                            <td><h3  class="mb-0 mt-0">{{ number_format($customer,2)}}</h3> </td>
                                            <td><h3  class="mb-0 mt-0">{{ number_format($supplier,2)}}</h3> </td>



                                        </tr>
                                    </table>
                                @endcomponent
                                @component('components.widget', ['class' => 'box-solid'])
                                        <table class="table no-border">
                                            <tr>
                                                <td>Total Purchase Price:</td>
                                                <td>Total Sell Price:</td>
                                                <td>Number of Shares</td>
                                               </tr>
                                            <tr>


                                                <td><h3  class="mb-0 mt-0">{{ number_format($closing_stock_by_pp+$partnerassets+$customer-$supplier,2)}}</h3> </td>
                                                <td><h3  class="mb-0 mt-0">{{ number_format($closing_stock_by_sp+$partnerassets+$customer-$supplier,2)}}</h3> </td>
                                                <td><h3  class="mb-0 mt-0">{{ number_format($totalshare,2)}}</h3> </td>



                                            </tr>
                                        </table>
                                    @endcomponent
                                    @if($totalshare>0)
                                      @component('components.widget', ['class' => 'box-solid'])
                                        <table class="table no-border">
                                            <tr>
                                                <td>Share Price at Purchase Price:</td>
                                                <td>Share Price at Selling Price:</td>

                                            </tr>
                                            <tr>


                                                <td><h2  class="mb-0 mt-0">{{ number_format(($closing_stock_by_pp+$partnerassets+$customer-$supplier)/$totalshare,2)}}</h2> </td>
                                                <td><h2  class="mb-0 mt-0">{{ number_format(($closing_stock_by_sp+$partnerassets+$customer-$supplier)/$totalshare,2)}}</h2> </td>




                                            </tr>
                                        </table>
                                    @endcomponent
                                    @endif
                            </div>
                        </div>
                  @endslot
            @endcan
            @can('assets.view')




            @endcan
        @endcomponent



    </section>

    <div class="modal fade brands_modal" tabindex="-1" role="dialog"
         aria-labelledby="gridSystemModalLabel">
    </div>
@endsection

<script>

    function assetedit(id) {
        $.ajax({
            url: '/partners/partners/'+id+'/edit',
            dataType: 'html',
            success: function(result) {
                $(".brands_modal").html(result)
                    .modal('show');
            },
        });
    }


    function  deleteasset(id) {
        swal({
            title: LANG.sure,
            text: 'Do you want to delete the Partner?',
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = '/partners/partners/'+id;
                var data = id;
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data:{
                        data:data
                    },
                    success: function(result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                            var drow = document.getElementById(id);
                            drow.parentNode.removeChild(drow);
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    }

</script>

