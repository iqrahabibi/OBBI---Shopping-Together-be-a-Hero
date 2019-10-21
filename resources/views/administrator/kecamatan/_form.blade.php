<div class="form-group {!! $errors->has('kota_id') ? 'has-error' : '' !!}">
	{!! Form::label('kota_id', 'Kota', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		@if($data != '')
			{!! Form::text('kota_id', $data->kota->nama_kota, ['class'=>'form-control', 'disabled'=>'disabled']) !!}
		@else
			{!! Form::select('kota_id',[''=>''], null,['class'=>'js-selectize', 'placeholder'=>'Pilih Kota', 'disabled'=>isset($data->kota_id) ? 'disabled' : null]) !!}
		@endif
		<div class="form-control-focus"></div>
		{!! $errors->first('kota_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('nama_kecamatan') ? 'has-error' : '' !!}">
	{!! Form::label('nama_kecamatan', 'Nama Kecamatan', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('nama_kecamatan', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('nama_kecamatan', '<p class="help-block">:message</p>') !!}
	</div>
</div>