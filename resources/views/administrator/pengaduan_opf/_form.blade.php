<div class="form-group {!! $errors->has('user_id') ? 'has-error' : '' !!}">
	{!! Form::label('user_id', 'User') !!}
		{!! Form::text('user_id', $data->user->fullname, ['class'=>'form-control', 'disabled'=>'disabled']) !!}
	{!! $errors->first('user_id', '<p class="help-block">:message</p>') !!}
</div>