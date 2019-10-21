<div class="form-group {!! $errors->has('nama_suplier') ? 'has-error' : '' !!}">
	{!! Form::label('nama_suplier', 'Nama Suplier', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('nama_suplier', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('nama_suplier', '<p class="help-block">:message</p>') !!}
	</div>
</div>