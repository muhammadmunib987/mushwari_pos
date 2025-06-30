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
        <h1>Payments</h1>
    </section>

    <section class="content">
        @component('components.widget', ['class' => 'box-primary', 'title' =>''])
            @can('assets.create')
                @slot('tool')
                    <div class="row">
                        <div class="col-lg-2 col-md-3">
                            <div class="form-group">
                                {!! Form::label('partner_id','Parter') !!}
                                {!! Form::select('partner_id', $partners, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' =>'All']); !!}
                            </div>
                        </div>


                        <div class="col-lg-2 col-md-3">
                            <div class="form-group">
                                {!! Form::label('startdate','From:') !!}
                                <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                               {!! Form::text('startdate', null, ['class' => 'form-control start-date-picker','placeholder' => __('partners.purchasedate'), 'readonly']); !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-3">
                            <div class="form-group">
                                {!! Form::label('startdate','To:') !!}
                                <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                    {!! Form::text('startdate', null, ['class' => 'form-control start-date-picker','placeholder' => __('partners.purchasedate'), 'readonly']); !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-3">
                            <div class="form-group">
                                {!! Form::label('type','Transaction Type') !!}
                                {!! Form::select('type', ['0'=>'All','1'=>'Withdraw','2'=>'Deposit'], null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                            </div>
                        </div>
                    </div>

                    <div class="box-tools">
                     <button type="button" class="btn btn-block btn-primary " onclick="addpayment()" >
                            <i class="fa fa-plus"></i> @lang( 'messages.add' )
                     </button>
                    </div>
                @endslot
            @endcan
            @can('assets.view')

                <div class="table-responsive">
                    <table class="table table-bordered table-striped " id="assete_table">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Value</th>
                            <th>History </th>
                            <th>Transaction Type </th>
                            <th>Added By</th>
                            <th>Notes</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody  id="datatable">



                        </tbody>

                    </table>
                </div>


            @endcan
        @endcomponent



    </section>

    <div class="modal fade datamodal" tabindex="-1" role="dialog"
         aria-labelledby="gridSystemModalLabel" id="modeldialog" >

    </div>


    {{--Create Model --}}


@endsection


@section('javascript')
<script>



    function deleteasset(id) {
        swal({
            title: LANG.sure,
            text: 'هل تريد حذف العملية',
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = '/partners/payments/' + id;
                var data = id;
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: {
                        data: data
                    },
                    success: function (result) {
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

    $(document).ready( function() {
        getdata();
        $(document).on('change','#partner_id , #type',function () {
            getdata();
        });

    });

    function getdata() {

        $.ajax({
            url: "{{action('\Modules\Partners\Http\Controllers\PaymentsController@getpayments')}}",
            method: 'GET',
            data: {
                partner_id:$('#partner_id').val()
                ,type:$('#type').val()

            },
            success: function (data) {
                document.getElementById("datatable").innerHTML = data;

            },
            error: function (data) {
                // Something went wrong
                // HERE you can handle asynchronously the response

                // Log in the console
                var errors = data.responseJSON;
                console.log(errors);

                // or, what you are trying to achieve
                // render the response via js, pushing the error in your
                // blade page
                errorsHtml = '<div class="alert alert-danger"><ul>';

                $.each(errors.error, function (key, value) {
                    errorsHtml += '<li>' + value[0] + '</li>'; //showing only the first error.
                });
                errorsHtml += '</ul></div>';

                $('#form-errors').html(errorsHtml); //appending to a <div id="form-errors"></div> inside form
            }


        });
    }

    function addpayment() {
        $.ajax({
            url: '/partners/payments/create',
            dataType: 'html',
            success: function(result) {
                $('#modeldialog')
                    .html(result)
                    .modal('show');
            },
        });
    }
    $(document).on('submit', 'form#addpayment', function(e) {
        e.preventDefault();
       $(this)
            .find('button[type="submit"]')
            .attr('disabled', true);
        var data = $(this).serialize();

        $.ajax({
            method: 'POST',
            url: $(this).attr('action'), // action('BrandController@store'
            dataType: 'json',
            data: data,
            success: function(result) {
                if (result.success == true) {
                    $('#modeldialog').modal('hide');
                    toastr.success(result.msg);
                    getdata();

                  } else {
                    toastr.error(result.msg);
                }
            },
            error: function (data) {
                // Something went wrong
                // HERE you can handle asynchronously the response

                // Log in the console
                var errors = data.responseJSON;
                console.log(errors);

                // or, what you are trying to achieve
                // render the response via js, pushing the error in your
                // blade page
                errorsHtml = '<div class="alert alert-danger"><ul>';

                $.each(errors.error, function (key, value) {
                    errorsHtml += '<li>' + value[0] + '</li>'; //showing only the first error.
                });
                errorsHtml += '</ul></div>';

                $('#form-errors').html(errorsHtml); //appending to a <div id="form-errors"></div> inside form
            }

        });
    });

    function paymentdit(id) {
        $.ajax({
            url: '/partners/payments/'+id+'/edit',
            dataType: 'html',
            success: function(result) {
                $("#modeldialog").html(result)
                    .modal('show');
            },
        });
    }






</script>

@endsection

