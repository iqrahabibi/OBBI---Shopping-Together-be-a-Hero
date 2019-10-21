<div class="form-group {!! $errors->has('nama_provinsi') ? 'has-error' : '' !!}">
	{!! Form::label('nama_provinsi', 'Nama Provinsi', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('nama_provinsi', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('nama_provinsi', '<p class="help-block">:message</p>') !!}
	</div>
</div>