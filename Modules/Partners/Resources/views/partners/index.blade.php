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
        <h1>Partners</h1>
    </section>

    <section class="content">
        @component('components.widget', ['class' => 'box-primary', 'title' =>''])
            @can('assets.create')
                @slot('tool')
                    <div class="box-tools">


                        {{-- <a href="{{action('\Modules\Partners\Http\Controllers\PartnerAssetsController@create')}}" class="btn btn-block btn-primary">
                             <i class="fa fa-plus"></i>@lang( 'messages.add' )
                         </a>--}}

                        <button type="button" class="btn btn-block btn-primary btn-modal"
                                data-href="{{action('\Modules\Partners\Http\Controllers\PartnersController@create')}}"
                                data-container=".brands_modal">
                            <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                    </div>
                @endslot
            @endcan
            @can('assets.view')
                @php
                    $status=array('','New','Used','Rented');
                @endphp
                <div class="table-responsive">
                    <table class="table table-bordered table-striped " id="assete_table">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Phone </th>
                            <th>Number of Shares </th>
                            <th>Credit</th>
                            <th>Debit </th>
                            {{--<th> Withdrawal Value</th>--}}
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody  id="datatable">
                       @foreach($partners as $partner)
                            <tr id="{{$partner->id}}">
                                <td>{{$partner->name}}</td>
                                <td>{{$partner->address }}</td>
                                <td>{{$partner->mobile}}</td>
                                <td>{{$partner->share}}</td>
                                <td>@if($partner-> value<0) {{abs($partner-> value)}} @endif</td>
                                <td>@if($partner-> value>0) {{abs($partner-> value)}} @endif</td>
                               {{-- <td>0</td>--}}
                                <td>
                                    <button onclick="assetedit({{$partner->id}})"  class="btn btn-xs btn-primary btn-modal"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                                    <button onclick="deleteasset({{$partner->id}})" class="btn btn-xs btn-danger delete_asset_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>

                                </td>
                            </tr>
                        @endforeach

                      <tr id="0" >
                            <th colspan="3">Total: </th>

                            <th>{{$totalshare}}</th>
                            {{--<th>0</th>--}}

                            <th colspan="3"></th>

                        </tr>
                        </tbody>

                    </table>
                </div>


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
            text: 'Are you sure to Delete?',
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

