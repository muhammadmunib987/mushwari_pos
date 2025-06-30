<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Partners\Http\Controllers\PaymentsController@store'), 'method' => 'post','id' =>'addpayment' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Partner Payments</h4>
        </div>

        <div class="modal-body">
            <div class="form-group">
                {!! Form::label('name','Partner Name:*') !!}
                {!! Form::select('partner_id', $partners, null, ['class' => 'form-control select2', 'style' => 'width:100%;height: 40px;']); !!}
            </div>

            <div class="form-group">
                {!! Form::label('value','Value:*') !!}
                {!! Form::text('value', null, ['class' => 'form-control', 'required', 'placeholder' =>'Total Value' ]); !!}
            </div>

            <div class="form-group">
                <div class="form-group">
                    {!! Form::label('type','Transaction Type') !!}
                    {!! Form::select('type', ['1'=>'Withdraw','2'=>'Deposit'], null, ['class' => 'form-control select2', 'style' => 'width:100%;height: 40px;']); !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('date','Date: ') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>

                    {!! Form::text('date', null, ['class' => 'form-control date-picker','required','placeholder' => __('partners.purchasedate'), 'readonly']); !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('notes',' Notes:') !!}
                {!! Form::text('notes', null, ['class' => 'form-control',  'placeholder' =>'Notes' ]); !!}
            </div>

        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $('.date-picker').datepicker({
        autoclose: true,
        endDate: 'today',
        format:'yyyy-m-d',
    });
</script>