<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Partners\Http\Controllers\FinalAccountController@update',$data->id),'id'=>'edit', 'method' => 'PUT' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Edit Final Account</h4>
        </div>

        <div class="modal-body">




            <div class="form-group">
                {!! Form::label('profite','Profit Value:') !!}
                {!! Form::text('profite', $data->profite, ['class' => 'form-control decimal', 'required', 'placeholder' =>'Total Value' ]); !!}
            </div>
            <div class="form-group">
                {!! Form::label('sharenumber','Total Number of Shares: ') !!}
                {!! Form::text('sharenumber', $data->sharenumber, ['class' => 'form-control', 'readonly', 'placeholder' =>'' ]); !!}
            </div>

            <div class="form-group">
                {!! Form::label('shareval','Share Value  :') !!}
                {!! Form::text('shareval',number_format($data->profite/$data->sharenumber,2), ['class' => 'form-control', 'readonly', 'placeholder' =>'' ]); !!}
            </div>

            <div class="form-group">
                {!! Form::label('startdate','Start Date  :') !!}
                <div class="input-group">
                            <span class="input-group-addon">
                                   <i class="fa fa-calendar"></i>
                             </span>
                    {!! Form::text('startdate', $data->startdate, ['class' => 'form-control date-picker', 'required', 'placeholder' =>'Start Date' ]); !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('enddate','End Date :') !!}
                <div class="input-group">
                            <span class="input-group-addon">
                                   <i class="fa fa-calendar"></i>
                             </span>
                    {!! Form::text('enddate', $data->enddate, ['class' => 'form-control date-picker', 'required', 'placeholder' =>'End Date' ]); !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('notes','Notes: ') !!}
                {!! Form::text('notes', $data->notes, ['class' => 'form-control' ]); !!}
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
        format:'yyyy-m-d',
    });


    $("#profite").on('keyup',function () {
        var total=$(this).val();
        var number=$('#sharenumber').val();
        var sharval=(total/number).toFixed(2);
        $('#shareval').val(sharval);

        $('.share').each(function (index,item) {
            var id = $(this).attr('id');
            var remval=$(this).val()*sharval -$('#value_'+id).val();
            $('#rem_'+id).val(remval.toFixed(2));

        });


    });

</script>