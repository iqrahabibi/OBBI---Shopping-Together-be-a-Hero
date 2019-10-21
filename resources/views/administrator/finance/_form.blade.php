<div class="form-group {!! $errors->has('kode') ? 'has-error' : '' !!}">
	{!! Form::label('kode', 'Kode', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('kode', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('kode', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('deskripsi') ? 'has-error' : '' !!}">
	{!! Form::label('deskripsi', 'Deskripsi', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('deskripsi', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('deskripsi', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('keuntungan') ? 'has-error' : '' !!}">
	{!! Form::label('keuntungan', 'Keuntungan', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('keuntungan', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('keuntungan', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('amal') ? 'has-error' : '' !!}">
	{!! Form::label('amal', 'Amal', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('amal', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('amal', '<p class="help-block">:message</p>') !!}
	</div>
</div>