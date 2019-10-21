<div class="form-group {!! $errors->has('karyawan_id') ? 'has-error' : '' !!}">
	{!! Form::label('karyawan_id', 'Karyawan', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::select('karyawan_id',[''=>'']+App\Model\Karyawan::list_for_absensi(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih Karyawan']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('karyawan_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('tanggal_absen') ? 'has-error' : '' !!}">
	{!! Form::label('tanggal_absen', 'Tanggal Absensi', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::date('tanggal_absen', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('tanggal_absen', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('absen') ? 'has-error' : '' !!}">
	{!! Form::label('absen', 'Absen', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('absen', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('absen', '<p class="help-block">:message</p>') !!}
	</div>
</div>


<!-- <div class="form-group {!! $errors->has('absen_masuk') ? 'has-error' : '' !!}">
	{!! Form::label('absen_masuk', 'Absen Masuk') !!}
	{!! Form::time('absen_masuk', null, ['class'=>'form-control']) !!}
	{!! $errors->first('absen_masuk', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {!! $errors->has('absen_keluar') ? 'has-error' : '' !!}">
	{!! Form::label('absen_keluar', 'Absen Masuk') !!}
	{!! Form::time('absen_keluar', null, ['class'=>'form-control']) !!}
	{!! $errors->first('absen_keluar', '<p class="help-block">:message</p>') !!}
</div> -->