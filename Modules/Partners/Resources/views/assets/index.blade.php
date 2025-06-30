@extends('layouts.app')
@section('title','Fixed Assets')

@section('content')
<style>
    .table-striped th{
        background-color: #626161;
        color: #ffffff;
    }
</style>

    @include('partners::layouts.nav')

     <section class="content-header">
         <h1>Fixed Assets</h1>
     </section>

 <section class="content">
     @component('components.widget', ['class' => 'box-primary', 'title' =>'Company Fixed Assets'])
         @can('partnerassets.create')
             @slot('tool')
                 <div class="box-tools">


                    {{-- <a href="{{action('\Modules\Partners\Http\Controllers\PartnerAssetsController@create')}}" class="btn btn-block btn-primary">
                         <i class="fa fa-plus"></i>@lang( 'messages.add' )
                     </a>--}}

                    <button type="button" class="btn btn-block btn-primary btn-modal"
                             data-href="{{action('\Modules\Partners\Http\Controllers\PartnerAssetsController@create')}}"
                             data-container=".brands_modal">
                         <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                 </div>
             @endslot
         @endcan
         @can('partnerassets.view')
             @php
             $status=array('','New','Used','Rented');
             @endphp
                 <div class="table-responsive">
                     <table class="table table-bordered table-striped " id="assete_table">
                         <thead>
                         <tr>
                             <th>Origin Code</th>
                             <th>Quantity</th>
                             <th>Description</th>
                             <th>Date Added</th>
                             <th>Purchase Price</th>
                             <th>Current Price</th>
                             <th>Modification Date</th>
                             <th>Origin Condition</th>
                             <th>Action</th>
                         </tr>
                         </thead>
                         <tbody  id="datatable">
                         @foreach($partnerassets as $partnerasset)
                             <tr id="{{$partnerasset->id}}">
                                 <td>{{$partnerasset->assetcode}}</td>
                                 <td>{{$partnerasset->quantity }}</td>
                                 <td>{{$partnerasset->description}}</td>
                                 <td>{{$partnerasset->purchasedate}}</td>
                                 <td>{{$partnerasset->price}}</td>
                                 <td>{{$partnerasset->curentprice}}</td>
                                 <td>{{$partnerasset->changedate}}</td>
                                 <td>{{$status[$partnerasset->status]}}</td>
                                 <td>
                                     <button onclick="assetedit({{$partnerasset->id}})"  class="btn btn-xs btn-primary btn-modal"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                                     <button onclick="deleteasset({{$partnerasset->id}})" class="btn btn-xs btn-danger delete_asset_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>

                                 </td>
                             </tr>
                         @endforeach

                         <tr id="0" >
                             <th colspan="4">Total : </th>

                             <th>{{$price}}</th>
                             <th>{{$curentprice}}</th>
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
             url: '/partners/assets/'+id+'/edit',
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
            text: 'Are you sure to delete',
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = '/partners/assets/'+id;
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

