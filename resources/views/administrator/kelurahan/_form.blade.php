<div class="form-group {!! $errors->has('kecamatan_id') ? 'has-error' : '' !!}">
	{!! Form::label('kecamatan_id', 'Kecamatan', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		@if($data != '')
			{!! Form::text('kecamatan_id', $data->kecamatan->nama_kecamatan, ['class'=>'form-control', 'disabled'=>'disabled']) !!}
		@else
			{!! Form::select('kecamatan_id',[''=>''], null,['class'=>'js-selectize', 'placeholder'=>'Pilih Kecamatan', 'disabled'=>isset($data->kecamatan_id) ? 'disabled' : null]) !!}
		@endif
		<div class="form-control-focus"></div>
		{!! $errors->first('kecamatan_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('nama_kelurahan') ? 'has-error' : '' !!}">
	{!! Form::label('nama_kelurahan', 'Nama Kelurahan', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('nama_kelurahan', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('nama_kelurahan', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('kode_pos') ? 'has-error' : '' !!}">
	{!! Form::label('kode_pos', 'Kode POS', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('kode_pos', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('kode_pos', '<p class="help-block">:message</p>') !!}
	</div>
</div>
