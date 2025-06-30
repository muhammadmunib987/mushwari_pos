<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Partners\Http\Controllers\PartnerAssetsController@update',$partnerasset->id), 'method' => 'PUT' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Edit Asset</h4>
        </div>

        <div class="modal-body">
            <div class="form-group">
                {!! Form::label('assetcode', 'Asset Code: *') !!}
                {!! Form::text('assetcode', $partnerasset->assetcode, ['class' => 'form-control', 'required', 'placeholder' => 'Asset Code' ]); !!}
            </div>
            <div class="form-group">
                {!! Form::label('quantity', 'Quantity:*') !!}
                {!! Form::text('quantity',$partnerasset->quantity, ['class' => 'form-control', 'required', 'placeholder' => 'Quantity' ]); !!}
            </div>

            <div class="form-group">
                {!! Form::label('description', 'Description:') !!}
                {!! Form::text('description',  $partnerasset->description, ['class' => 'form-control','placeholder' => 'Description']); !!}
            </div>

            <div class="form-group">
                {!! Form::label('purchasedate', 'Purchase Date:') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>

                    {!! Form::text('purchasedate', $partnerasset->purchasedate, ['class' => 'form-control date-picker','placeholder' =>  'Purchase Date', 'readonly']); !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('price','Asset Price:') !!}
                {!! Form::text('price',  $partnerasset->price, ['class' => 'form-control','placeholder' => 'Asset Price']); !!}
            </div>

            <div class="form-group">
                {!! Form::label('curentprice', 'Current Price:') !!}
                {!! Form::text('curentprice', $partnerasset->curentprice, ['class' => 'form-control','placeholder' => 'Current Price']); !!}
            </div>

            <div class="form-group">
                {!! Form::label('changedate', 'Modification Date:') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>

                    {!! Form::text('changedate',  $partnerasset->changedate, ['class' => 'form-control date-picker','placeholder' => 'Modification Date', 'readonly']); !!}
                </div>
            </div>


            <div class="form-group">
                {!! Form::label('Status') !!}
                <div class="input-group">
                    <select id="status" name="status" class="form-control">
                        <option value="1"  @if($partnerasset->status==1) selected @endif>New</option>
                        <option value="2"  @if($partnerasset->status==2) selected @endif>Used</option>
                        <option value="3"  @if($partnerasset->status==3) selected @endif>Rented</option>
                    </select>

                </div>
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