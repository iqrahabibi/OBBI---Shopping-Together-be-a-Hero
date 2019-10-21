
<div class="form-group {!! $errors->has('fullname') ? 'has-error' : '' !!}">
	{!! Form::label('fullname', 'Fullname', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('fullname', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('fullname', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('email') ? 'has-error' : '' !!}">
	{!! Form::label('email', 'Email', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('email', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('email', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('password') ? 'has-error' : '' !!}">
	{!! Form::label('password', 'Password', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('password', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('password', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('phone') ? 'has-error' : '' !!}">
	{!! Form::label('phone', 'Nomor Handphone', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('phone', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('phone', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('role_id') ? 'has-error' : '' !!}">
	{!! Form::label('role_id', 'Pilih Role', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::select('role_id',[''=>'']+App\Model\Role::list_for_office(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih Role', 'disabled'=>isset($data->provinsi_id) ? 'disabled' : null]) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('role_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
