<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Partners\Http\Controllers\PartnersController@update',$partner->id), 'method' => 'PUT' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Edit Partner</h4>
        </div>

        <div class="modal-body">
            <div class="form-group">
                {!! Form::label('name','Partner Name :*') !!}
                {!! Form::text('name', $partner->name, ['class' => 'form-control', 'required', 'placeholder' =>'Name' ]); !!}
            </div>

            <div class="form-group">
                {!! Form::label('address',' Address:*') !!}
                {!! Form::text('address', $partner->address, ['class' => 'form-control', 'required', 'placeholder' =>'Address' ]); !!}
            </div>

            <div class="form-group">
                {!! Form::label('mobile',' Mobile:') !!}
                {!! Form::text('mobile', $partner->mobile, ['class' => 'form-control','placeholder' =>'Mobile']); !!}
            </div>

            <div class="form-group">
                {!! Form::label('share','Number of Shares:') !!}
                {!! Form::text('share', $partner->share, ['class' => 'form-control','placeholder' =>'Number of Shares']); !!}
            </div>


        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
