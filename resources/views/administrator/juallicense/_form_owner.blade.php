<div class="form-group">
	{!! Form::label('nik', 'NIK', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
        {!! Form::text('nik', $data->nik, ['class'=>'form-control', 'disabled'=>'disabled']) !!}
	</div>
</div>
<div class="form-group">
	{!! Form::label('nama_depan', 'Nama Depan', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
        {!! Form::text('nama_depan', $data->nama_depan, ['class'=>'form-control', 'disabled'=>'disabled']) !!}
	</div>
</div>
<div class="form-group">
    {!! Form::label('nama_tengah', 'Nama Tengah', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
        {!! Form::text('nama_tengah', $data->nama_tengah, ['class'=>'form-control', 'disabled'=>'disabled']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('nama_belakang', 'Nama Belakang', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
        {!! Form::text('nama_belakang', $data->nama_belakang, ['class'=>'form-control', 'disabled'=>'disabled']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('nama_lengkap', 'Nama Lengkap', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
        {!! Form::text('nama_lengkap', $data->nama_lengkap, ['class'=>'form-control', 'disabled'=>'disabled']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('tanggal_lahir', 'Tanggal Lahir', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
        {!! Form::text('tanggal_lahir', $data->tanggal_lahir, ['class'=>'form-control', 'disabled'=>'disabled']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('agama_id', 'Agama', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
        {!! Form::text('agama_id', $data->agama->nama_agama, ['class'=>'form-control', 'disabled'=>'disabled']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('status_pernikahan', 'Status Pernikahan', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
        {!! Form::text('status_pernikahan', $data->status_pernikahan, ['class'=>'form-control', 'disabled'=>'disabled']) !!}
    </div>
</div>
<div class="form-group {!! $errors->has('jenis_kelamin') ? 'has-error' : '' !!}">
	{!! Form::label('jenis_kelamin', 'Jenis Kelamin', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		<div class="md-radio-inline">
			<div class="md-radio">
				{!! Form::radio('jenis_kelamin', 'Laki-Laki', true, ['class'=>'md-radiobtn', 'id'=>'radio_laki', 'disabled'=>'disabled']) !!}
				<label for="radio_laki">
				<span></span>
				<span class="check"></span>
				<span class="box"></span>
				Laki-Laki</label>
			</div>
			<div class="md-radio">
				{!! Form::radio('jenis_kelamin', 'Perempuan', !empty($data) && $data->jenis_kelamin == 'Perempuan' ? true : false, ['class'=>'md-radiobtn', 'id'=>'radio_perempuan', 'disabled'=>'disabled']) !!}
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
<div class="form-group">
    {!! Form::label('alamat', 'Alamat', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
        {!! Form::text('alamat', $data->alamat, ['class'=>'form-control', 'disabled'=>'disabled']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('rt', 'RT', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
        {!! Form::text('rt', $data->rt, ['class'=>'form-control', 'disabled'=>'disabled']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('rw', 'RW', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
        {!! Form::text('rw', $data->rw, ['class'=>'form-control', 'disabled'=>'disabled']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('no_telp', 'Nomor Telpon 1', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
        {!! Form::text('no_telp', $data->no_telp, ['class'=>'form-control', 'disabled'=>'disabled']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('no_telp_2', 'Nomor Telpon 2', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
        {!! Form::text('no_telp_2', $data->no_telp_2, ['class'=>'form-control', 'disabled'=>'disabled']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('file_ktp', 'File KTP', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
        @if(isset($data) && $data->file_ktp)
            {!!Html::image(config('app.api').'/storage'.$data->file_ktp, null,['width'=>'300px','height'=>'300px'])!!}
        @else
            {!! Form::text('file_ktp', 'No File Found', ['class'=>'form-control', 'disabled'=>'disabled']) !!}
        @endif
    </div>
</div>
