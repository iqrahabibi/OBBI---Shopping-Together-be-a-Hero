<div class="form-group {!! $errors->has('nama_group') ? 'has-error' : '' !!}">
	{!! Form::label('nama_group', 'Nama Group', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('nama_group', null, ['class'=>'form-control']) !!}
		{!! $errors->first('nama_group', '<p class="help-block">:message</p>') !!}
	</div>
</div>