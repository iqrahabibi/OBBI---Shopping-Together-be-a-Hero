<div class="form-group {!! $errors->has('code') ? 'has-error' : '' !!}">
	{!! Form::label('code', 'Kode', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('code', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('code', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('name') ? 'has-error' : '' !!}">
	{!! Form::label('name', 'Nama', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('name', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('name', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('code_baru') ? 'has-error' : '' !!}">
	{!! Form::label('code_baru', 'Kode Baru', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('code_baru', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('code_baru', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('wajib') ? 'has-error' : '' !!}">
	{!! Form::label('wajib', 'Wajib', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('wajib', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('wajib', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('version') ? 'has-error' : '' !!}">
	{!! Form::label('version', 'Versi', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('version', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('version', '<p class="help-block">:message</p>') !!}
	</div>
</div>