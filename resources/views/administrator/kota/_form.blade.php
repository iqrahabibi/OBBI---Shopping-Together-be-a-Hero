<div class="form-group {!! $errors->has('provinsi_id') ? 'has-error' : '' !!}">
	{!! Form::label('provinsi_id', 'Provinsi', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		@if(!empty($data))
			{!! Form::text('provinsi_id', $data->provinsi->nama_provinsi, ['class'=>'form-control', 'disabled'=>'disabled', 'id'=>'form_control_1']) !!}
		@else
			{!! Form::select('provinsi_id',[''=>'']+App\Model\Provinsi::pluck('nama_provinsi','id')->all(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih Provinsi', 'disabled'=>isset($data->provinsi_id) ? 'disabled' : null, 'id'=>'form_control_1']) !!}
		@endif
		<div class="form-control-focus"></div>
		{!! $errors->first('provinsi_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('tipe') ? 'has-error' : '' !!}">
	{!! Form::label('tipe', 'Tipe', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::select('tipe',[''=>'', 'Kota'=>'Kota', 'Kab.'=>'Kab.'], null,['class'=>'js-selectize', 'placeholder'=>'Pilih Tipe']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('tipe', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('nama_kota') ? 'has-error' : '' !!}">
	{!! Form::label('form_control_1', 'Nama Kota', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('nama_kota', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('nama_kota', '<span class="help-block">:message</span>') !!}
	</div>
</div>