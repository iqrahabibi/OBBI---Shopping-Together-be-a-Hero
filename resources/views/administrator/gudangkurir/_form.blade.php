<div class="form-group {!! $errors->has('gudang_id') ? 'has-error' : '' !!}">
	{!! Form::label('gudang_id', 'Gudang', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::select('gudang_id',[''=>'']+App\Model\Gudang::pluck('nama_gudang','id')->all(), null,
			['class'=>'js-selectize', 'placeholder'=>'Pilih Gudang']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('gudang_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('nama') ? 'has-error' : '' !!}">
	{!! Form::label('nama', 'Nama Kurir', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::select('nama',[''=>'']+$list_kurir, null,
			['class'=>'js-selectize', 'placeholder'=>'Pilih Kurir']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('nama', '<p class="help-block">:message</p>') !!}
	</div>
</div>