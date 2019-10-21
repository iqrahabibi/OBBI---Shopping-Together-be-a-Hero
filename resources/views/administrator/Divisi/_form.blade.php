<div class="form-group {!! $errors->has('nama_divisi') ? 'has-error' : '' !!}">
	{!! Form::label('nama_divisi', 'Nama Divisi', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('nama_divisi', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('nama_divisi', '<p class="help-block">:message</p>') !!}
	</div>
</div>