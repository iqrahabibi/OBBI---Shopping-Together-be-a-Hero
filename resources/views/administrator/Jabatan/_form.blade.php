<div class="form-group {!! $errors->has('nama_jabatan') ? 'has-error' : '' !!}">
	{!! Form::label('nama_jabatan', 'Nama Jabatan', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('nama_jabatan', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('nama_jabatan', '<p class="help-block">:message</p>') !!}
	</div>
</div>