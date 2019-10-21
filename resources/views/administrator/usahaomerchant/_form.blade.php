<div class="form-group {!! $errors->has('usaha_id') ? 'has-error' : '' !!}">
	{!! Form::label('usaha_id', 'Usaha', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		@if(empty($data))
			{!! Form::select('usaha_id',[''=>'']+App\Model\Usaha::list(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih Usaha']) !!}
		@else
			{!! Form::text('show_nama_usaha', $data->usaha->nama($data->usaha_id), ['class'=>'form-control', 'readonly']) !!}
			{!! Form::hidden('usaha_id', $data->usaha_id, ['class'=>'form-control']) !!}
		@endif
		{!! $errors->first('usaha_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('o_merchant_id') ? 'has-error' : '' !!}">
	{!! Form::label('o_merchant_id', 'User OMerchant', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		@if(empty($data))
			{!! Form::select('o_merchant_id',[''=>'']+App\Model\OMerchant::list_for_usaha_omerchant(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih User OMerchant']) !!}
		@else
			{!! Form::text('show_nama_o_merchant', $data->o_merchant->nama($data->o_merchant_id), ['class'=>'form-control', 'readonly']) !!}
			{!! Form::hidden('o_merchant_id', $data->o_merchant_id, ['class'=>'form-control']) !!}
		@endif
		{!! $errors->first('o_merchant_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('modal') ? 'has-error' : '' !!}">
	{!! Form::label('modal', 'Modal', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('modal', null, ['class'=>'form-control']) !!}
		{!! $errors->first('modal', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('tanggal_masuk') ? 'has-error' : '' !!}">
	{!! Form::label('tanggal_masuk', 'Tanggal Masuk', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::date('tanggal_masuk', null, ['class'=>'form-control']) !!}
		{!! $errors->first('tanggal_masuk', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('tanggal_keluar') ? 'has-error' : '' !!}">
	{!! Form::label('tanggal_keluar', 'Tanggal Keluar', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::date('tanggal_keluar', null, ['class'=>'form-control']) !!}
		{!! $errors->first('tanggal_keluar', '<p class="help-block">:message</p>') !!}
	</div>
</div>
