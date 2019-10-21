<div class="form-group {!! $errors->has('divisi_id') ? 'has-error' : '' !!}">
	{!! Form::label('divisi_id', 'Divisi', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::select('divisi_id',[''=>'']+App\Model\Divisi::list_for_divisi(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih Divisi']) !!}
		{!! $errors->first('divisi_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('jabatan_id') ? 'has-error' : '' !!}">
	{!! Form::label('jabatan_id', 'Jabatan', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::select('jabatan_id',[''=>'']+App\Model\Jabatan::list_for_jabatan(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih jabatan']) !!}
		{!! $errors->first('jabatan_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('nama_karyawan') ? 'has-error' : '' !!}">
	{!! Form::label('nama_karyawan', 'Nama Karyawan', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('nama_karyawan', null, ['class'=>'form-control']) !!}
		{!! $errors->first('nama_karyawan', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('alamat') ? 'has-error' : '' !!}">
	{!! Form::label('alamat', 'Alamat', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('alamat', null, ['class'=>'form-control']) !!}
		{!! $errors->first('alamat', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('tanggal_lahir') ? 'has-error' : '' !!}">
	{!! Form::label('tanggal_lahir', 'Tanggal Lahir', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::date('tanggal_lahir', null, ['class'=>'form-control']) !!}
		{!! $errors->first('tanggal_lahir', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('tempat_lahir') ? 'has-error' : '' !!}">
	{!! Form::label('tempat_lahir', 'Tempat Lahir', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('tempat_lahir', null, ['class'=>'form-control']) !!}
		{!! $errors->first('tempat_lahir', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('handphone1') ? 'has-error' : '' !!}">
	{!! Form::label('handphone1', 'Handphone 1', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('handphone1', null, ['class'=>'form-control']) !!}
		{!! $errors->first('handphone1', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('handphone2') ? 'has-error' : '' !!}">
	{!! Form::label('handphone2', 'Handphone 2', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('handphone2', null, ['class'=>'form-control']) !!}
		{!! $errors->first('handphone2', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('tanggal_masuk') ? 'has-error' : '' !!}">
	{!! Form::label('tanggal_masuk', 'Tanggal Masuk', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::date('tanggal_masuk', null, ['class'=>'form-control']) !!}
		{!! $errors->first('tanggal_masuk', '<p class="help-block">:message</p>') !!}
	</div>
</div>




