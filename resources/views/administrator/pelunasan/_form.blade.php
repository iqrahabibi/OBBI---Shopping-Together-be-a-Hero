<div class="form-group {!! $errors->has('voucher_id') ? 'has-error' : '' !!}">
	{!! Form::label('voucher_id', 'Voucher', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		@if($data != '')
			{!! Form::text('voucher_id', $data->voucher->jml_voucher, ['class'=>'form-control', 'disabled'=>'disabled']) !!}
		@else
			{!! Form::select('voucher_id',[''=>'']+App\Model\Voucher::pluck('sisa','id')->all(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih Voucher', 'disabled'=>isset($data->voucher_id) ? 'disabled' : null]) !!}
		@endif
		{!! $errors->first('voucher_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('purchasing_order_id') ? 'has-error' : '' !!}">
	{!! Form::label('purchasing_order_id', 'Purchasing Order', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		@if($data != '')
			{!! Form::text('purchasing_order_id', $data->purchasing_order->nomor_po . ' ( ' . $data->purchasing_order->total_masuk . ' )', ['class'=>'form-control', 'disabled'=>'disabled']) !!}
		@else
			{!! Form::select('purchasing_order_id',[''=>'']+App\Model\PurchasingOrder::list_for_pelunasan(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih PO', 'disabled'=>isset($data->po_id) ? 'disabled' : null]) !!}
		@endif
		{!! $errors->first('purchasing_order_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('tanggal') ? 'has-error' : '' !!}">
	{!! Form::label('tanggal', 'Tanggal', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::date('tanggal', null, ['class'=>'form-control']) !!}
		{!! $errors->first('tanggal', '<p class="help-block">:message</p>') !!}
	</div>
</div>