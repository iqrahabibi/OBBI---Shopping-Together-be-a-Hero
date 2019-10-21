<div class="form-group {!! $errors->has('barang_id') ? 'has-error' : '' !!}">
	{!! Form::label('barang_id', 'Barang', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		@if(!empty($data))
			{!! Form::text('nama_barang', $data->barang->nama_barang, ['class'=>'form-control', 'readonly']) !!}
			{!! Form::hidden('barang_id', $data->barang_id, ['class'=>'form-control']) !!}
		@else
			{!! Form::select('barang_id',[''=>'']+App\Model\Barang::list(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih Barang', 'disabled'=>isset($data->barang_id) ? 'disabled' : null]) !!}
		@endif
		{!! $errors->first('barang_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('varian_barang') ? 'has-error' : '' !!}">
	{!! Form::label('varian_barang', 'Varian Barang', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('varian_barang',null, ['class'=>'form-control']) !!}
		{!! $errors->first('varian_barang', '<p class="help-block">:message</p>') !!}
	</div>
</div>