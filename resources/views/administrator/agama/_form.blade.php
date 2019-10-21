<div class="form-group {!! $errors->has('nama_agama') ? 'has-error' : '' !!}">
	{!! Form::label('nama_agama', 'Nama Agama', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('nama_agama', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('nama_agama', '<p class="help-block">:message</p>') !!}
	</div>
</div>