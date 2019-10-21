<div class="form-group {!! $errors->has('o_merchant_id') ? 'has-error' : '' !!}">
	{!! Form::label('o_merchant_id', 'OMerchant', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::select('o_merchant_id',[''=>'']+App\Model\OMerchant::list_for_tabungan(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih OMerchant']) !!}
		{!! $errors->first('o_merchant_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('usaha_o_merchant_id') ? 'has-error' : '' !!}">
	{!! Form::label('usaha_o_merchant_id', 'Usaha OMerchant', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::select('usaha_o_merchant_id',[''=>''], null,['class'=>'js-selectize', 'placeholder'=>'Pilih Usaha OMerchant']) !!}
		{!! $errors->first('usaha_o_merchant_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('jumlah') ? 'has-error' : '' !!}">
	{!! Form::label('jumlah', 'Jumlah Tabungan', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('jumlah', null, ['class'=>'form-control']) !!}
		{!! $errors->first('jumlah', '<p class="help-block">:message</p>') !!}
	</div>
</div>