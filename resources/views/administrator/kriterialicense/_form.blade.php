<div class="form-group {!! $errors->has('nama_kriteria_license') ? 'has-error' : '' !!}">
	{!! Form::label('nama_kriteria_license', 'Nama Kriteria License', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('nama_kriteria_license', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('nama_kriteria_license', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('kode') ? 'has-error' : '' !!}">
	{!! Form::label('kode', 'Kode Kriteria', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('kode', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('kode', '<p class="help-block">:message</p>') !!}
	</div>
</div>