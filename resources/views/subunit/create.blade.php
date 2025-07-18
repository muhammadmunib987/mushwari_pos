<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action([\App\Http\Controllers\UnitController::class, 'subunitstore']), 'method' => 'post', 'id' => $quick_add ? 'quick_add_subunit_form' : 'subunit_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">Add Sub Unit</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="form-group col-sm-12">
          {!! Form::label('actual_name', __( 'unit.name' ) . ':*') !!}
            {!! Form::text('actual_name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'unit.name' )]); !!}
        </div>

        <div class="form-group col-sm-12">
          {!! Form::label('short_name', __( 'unit.short_name' ) . ':*') !!}
            {!! Form::text('short_name', null, ['class' => 'form-control', 'placeholder' => __( 'unit.short_name' ), 'required']); !!}
        </div>

        <div class="form-group col-sm-12">
          {!! Form::label('allow_decimal', __( 'unit.allow_decimal' ) . ':*') !!}
            {!! Form::select('allow_decimal', ['1' => __('messages.yes'), '0' => __('messages.no')], null, ['placeholder' => __( 'messages.please_select' ), 'required', 'class' => 'form-control']); !!}
        </div>
        <div class="form-group col-sm-12">
          <label for="unit_qty">Quantity</label>
            <input class="form-control" placeholder="Unit Quantity" name="base_unit_multiplier" type="text" id="base_unit_multiplier" fdprocessedid="waglho">
        </div>
        <div class="form-group col-sm-12">
          <label for="unit_sku">SKU</label>
            <input class="form-control" placeholder="Unit SKU"  name="unit_sku" type="text" id="unit_sku" fdprocessedid="waglho">
        </div>
        <div class="form-group col-sm-12">
          <label for="unit_sku"></label>
            <input class="form-control" placeholder="Unit Id" name="base_unit_id" type="hidden" id="subunit_base_unit_id" fdprocessedid="waglho">
            <input class="form-control" placeholder="Unit Id" value="1"  name="define_base_unit" type="hidden" id="unit_id" fdprocessedid="waglho">
        </div>
        @if(!$quick_add)
          <div class="form-group col-sm-12">
            <div class="form-group">
                <div class="checkbox">
                  <label>
                     {!! Form::checkbox('define_base_unit', 1, false,[ 'class' => 'toggler', 'data-toggle_id' => 'base_unit_div' ]); !!} @lang( 'lang_v1.add_as_multiple_of_base_unit' )
                  </label> @show_tooltip(__('lang_v1.multi_unit_help'))
                </div>
            </div>
          </div>
          <div class="form-group col-sm-12 hide" id="base_unit_div">
            <table class="table">
              <tr>
                <th style="vertical-align: middle;">1 <span id="unit_name">@lang('product.unit')</span></th>
                <th style="vertical-align: middle;">=</th>
                <td style="vertical-align: middle;">
                  {!! Form::text('base_unit_multiplier', null, ['class' => 'form-control input_number', 'placeholder' => __( 'lang_v1.times_base_unit' )]); !!}</td>
                <td style="vertical-align: middle;">
                  {!! Form::select('base_unit_id', $units, null, ['placeholder' => __( 'lang_v1.select_base_unit' ), 'class' => 'form-control']); !!}
                </td>
              </tr>
            </table>
          </div>
        @endif
      </div>

    </div>

    <div class="modal-footer">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang( 'messages.save' )</button>
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->