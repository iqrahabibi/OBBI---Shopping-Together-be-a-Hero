<div class="form-group {!! $errors->has('karyawan_id') ? 'has-error' : '' !!}">
	{!! Form::label('karyawan_id', 'Karyawan', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
	{!! Form::select('karyawan_id',[''=>'']+App\Model\Karyawan::list_for_salary(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih Karyawan']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('karyawan_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('nominal') ? 'has-error' : '' !!}">
	{!! Form::label('nominal', 'Nominal', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('nominal', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('nominal', '<p class="help-block">:message</p>') !!}
	</div>
</div>