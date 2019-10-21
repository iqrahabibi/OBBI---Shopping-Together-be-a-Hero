<div class="form-group {!! $errors->has('user_id') ? 'has-error' : '' !!}">
	{!! Form::label('user_id', 'User', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::select('user_id',[''=>'']+App\Model\User::list_for_admin_omerchant(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih User']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('user_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('level') ? 'has-error' : '' !!}">
	{!! Form::label('level', 'Hak Akses', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::select('level',[''=>'','1'=>'Kepala','2'=>'Karyawan'], null,['class'=>'js-selectize', 'placeholder'=>'Pilih User']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('level', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('kode') ? 'has-error' : '' !!}">
	{!! Form::label('kode', 'Usaha OMerchant', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::select('kode',[''=>'']+App\Model\UsahaOMerchant::list(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih Usaha OMerchant']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('kode', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('gudang_id') ? 'has-error' : '' !!}">
	{!! Form::label('gudang_id', 'Gudang', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::select('gudang_id',[''=>'']+App\Model\Gudang::pluck('nama_gudang','id')->all(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih Gudang']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('gudang_id', '<p class="help-block">:message</p>') !!}
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