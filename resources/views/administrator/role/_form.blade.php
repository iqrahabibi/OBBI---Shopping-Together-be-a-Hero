<div class="form-group {!! $errors->has('name') ? 'has-error' : '' !!}">
	{!! Form::label('name', 'Nama Role', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('name', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('name', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('description') ? 'has-error' : '' !!}">
	{!! Form::label('description', 'Deskripsi', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('description', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('description', '<p class="help-block">:message</p>') !!}
	</div>
</div>