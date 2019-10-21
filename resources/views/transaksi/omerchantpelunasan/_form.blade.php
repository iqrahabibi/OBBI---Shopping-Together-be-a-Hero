<div class="form-group {!! $errors->has('om_voucher_id') ? 'has-error' : '' !!}">
	{!! Form::label('om_voucher_id', 'Voucher') !!}
	@if($data != '')
		{!! Form::text('om_voucher_id', $data->om_voucher->jml_om_voucher, ['class'=>'form-control', 'disabled'=>'disabled']) !!}
	@else
		{!! Form::select('om_voucher_id',[''=>'']+App\Model\OMVoucher::list_voucher(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih OMerchant Voucher', 'disabled'=>isset($data->om_voucher_id) ? 'disabled' : null]) !!}
	@endif
	{!! $errors->first('om_voucher_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {!! $errors->has('ompo_id') ? 'has-error' : '' !!}">
	{!! Form::label('ompo_id', 'Purchasing Order') !!}
	@if($data != '')
		{!! Form::text('om_po_id', $data->om_po->nomor_po . ' ( ' . $data->om_po->total_masuk . ' )', ['class'=>'form-control', 'disabled'=>'disabled']) !!}
	@else
		{!! Form::select('om_po_id',[''=>'']+App\Model\OMerchantPo::list_for_om_pelunasan(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih OMPO', 'disabled'=>isset($data->om_po_id) ? 'disabled' : null]) !!}
	@endif
	{!! $errors->first('om_po_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {!! $errors->has('tanggal') ? 'has-error' : '' !!}">
	{!! Form::label('tanggal', 'Tanggal') !!}
	{!! Form::date('tanggal', null, ['class'=>'form-control']) !!}
	{!! $errors->first('tanggal', '<p class="help-block">:message</p>') !!}
</div>