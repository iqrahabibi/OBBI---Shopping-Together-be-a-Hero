<div class="form-group {!! $errors->has('menuname') ? 'has-error' : '' !!}">
	{!! Form::label('menuname', 'Nama Menu', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('menuname', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('menuname', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('url') ? 'has-error' : '' !!}">
	{!! Form::label('url', 'URL', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('url', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('url', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('parent') ? 'has-error' : '' !!}">
	{!! Form::label('parent', 'Parent', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::select('parent', ['0'=>'---']+App\Model\Menu::list(),null, ['class'=>'js-selectize']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('parent', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('level') ? 'has-error' : '' !!}">
	{!! Form::label('level', 'Level', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('level', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('level', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('icon') ? 'has-error' : '' !!}">
	{!! Form::label('icon', 'Icon', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('icon', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('icon', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('resource') ? 'has-error' : '' !!}">
	{!! Form::label('resource', 'Resource', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('resource', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('resource', '<p class="help-block">:message</p>') !!}
	</div>
</div>