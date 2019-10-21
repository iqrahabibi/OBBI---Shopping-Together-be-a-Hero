<div class="form-group {!! $errors->has('role_id') ? 'has-error' : '' !!}">
	{!! Form::label('name', 'Nama Role', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
        {!! Form::text('name', App\Model\Role::setrole($id)->name, ['class'=>'form-control','readonly']) !!}
        {!! Form::hidden('role_id',App\Model\Role::setrole($id)->id,['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
        {!! $errors->first('role_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('menu_id') ? 'has-error' : '' !!}">
	{!! Form::label('menu_id', 'Menu', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::select('menu_id', ['' => '']+App\Model\Menu::setmenu(),null, ['class'=>'js-selectize']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('menu_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>