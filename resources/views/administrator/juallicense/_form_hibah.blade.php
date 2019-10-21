<div class="caption font-green">
	<span class="caption-subject bold uppercase">License Owner</span>
</div>
<div class="form-group {!! $errors->has('nik_pewaris') ? 'has-error' : '' !!}">
	{!! Form::label('nik_pewaris', 'NIK Pewaris', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('nik_pewaris', isset($data) ? $data->license_owner->nik : null, ['class'=>'form-control', 'readonly']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('nik_pewaris', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('nik') ? 'has-error' : '' !!}">
	{!! Form::label('nik', 'NIK', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('nik', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('nik_pewaris', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('nama_depan') ? 'has-error' : '' !!}">
	{!! Form::label('nama_depan', 'Nama Depan', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('nama_depan', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('nama_depan', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('nama_tengah') ? 'has-error' : '' !!}">
	{!! Form::label('nama_tengah', 'Nama Tengah', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('nama_tengah', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('nama_tengah', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('nama_belakang') ? 'has-error' : '' !!}">
	{!! Form::label('nama_belakang', 'Nama Belakang', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('nama_belakang', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('nama_belakang', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('nama_lengkap') ? 'has-error' : '' !!}">
	{!! Form::label('nama_lengkap', 'Nama Lengkap', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('nama_lengkap', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('nama_lengkap', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('tanggal_lahir') ? 'has-error' : '' !!}">
	{!! Form::label('tanggal_lahir', 'Tanggal Lahir', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::date('tanggal_lahir', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('tanggal_lahir', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('agama_id') ? 'has-error' : '' !!}">
	{!! Form::label('agama_id', 'Agama', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::select('agama_id',[''=>'']+App\Model\Agama::pluck('nama_agama','id')->all(), null, ['class'=>'js-selectize', 'placeholder'=>'Pilih Agama']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('agama_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('status_pernikahan') ? 'has-error' : '' !!}">
	{!! Form::label('status_pernikahan', 'Status Pernikahan', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('status_pernikahan', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('status_pernikahan', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('jenis_kelamin') ? 'has-error' : '' !!}">
	{!! Form::label('jenis_kelamin', 'Jenis Kelamin', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		<div class="md-radio-inline">
			<div class="md-radio">
				{!! Form::radio('jenis_kelamin', 'Laki-Laki', true, ['class'=>'md-radiobtn', 'id'=>'radio_laki']) !!}
				<label for="radio_laki">
				<span></span>
				<span class="check"></span>
				<span class="box"></span>
				Laki-Laki</label>
			</div>
			<div class="md-radio">
				{!! Form::radio('jenis_kelamin', 'Perempuan', false, ['class'=>'md-radiobtn', 'id'=>'radio_perempuan']) !!}
				<label for="radio_perempuan">
				<span></span>
				<span class="check"></span>
				<span class="box"></span>
				Perempuan</label>
			</div>
		</div>
		<div class="form-control-focus"></div>
		{!! $errors->first('jenis_kelamin', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('alamat') ? 'has-error' : '' !!}">
	{!! Form::label('alamat', 'Alamat', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('alamat', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('alamat', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('rt') ? 'has-error' : '' !!}">
	{!! Form::label('rt', 'RT', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('rt', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('rt', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('rw') ? 'has-error' : '' !!}">
	{!! Form::label('rw', 'RW', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('rw', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('rw', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('no_telp') ? 'has-error' : '' !!}">
	{!! Form::label('no_telp', 'Nomor Telpon 1', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('no_telp', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('no_telp', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('no_telp_2') ? 'has-error' : '' !!}">
	{!! Form::label('no_telp_2', 'Nomor Telpon 2', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('no_telp_2', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('no_telp_2', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('file_ktp') ? 'has-error' : '' !!}">
	{!! Form::label('file_ktp', 'File KTP', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::file('file_ktp') !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('file_ktp', '<p class="help-block">:message</p>') !!}
	</div>
</div>

<div class="caption font-green">
	<span class="caption-subject bold uppercase">License Detail</span>
</div>
<div class="form-group {!! $errors->has('license_id') ? 'has-error' : '' !!}">
	{!! Form::label('license_id', 'License', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('license_id', isset($data) ? $data->license_id : null, ['class'=>'form-control', 'readonly']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('license_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('tanggal_jual') ? 'has-error' : '' !!}">
	{!! Form::label('tanggal_jual', 'Tanggal Jual', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::date('tanggal_jual', null, ['class'=>'form-control', 'readonly']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('tanggal_jual', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('perolehan') ? 'has-error' : '' !!}">
	{!! Form::label('perolehan', 'Perolehan', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('perolehan', null, ['class'=>'form-control', 'readonly']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('perolehan', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('jenis_pembayaran') ? 'has-error' : '' !!}">
	{!! Form::label('jenis_pembayaran', 'Jenis Pembayaran', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('jenis_pembayaran', null, ['class'=>'form-control', 'readonly']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('jenis_pembayaran', '<p class="help-block">:message</p>') !!}
	</div>
</div>
