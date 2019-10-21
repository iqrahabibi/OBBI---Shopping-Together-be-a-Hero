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
<div class="form-group {!! $errors->has('qty') ? 'has-error' : '' !!}">
	{!! Form::label('qty', 'Quantity', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('qty', null, ['class'=>'form-control']) !!}
		{!! $errors->first('qty', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('onhold_qty') ? 'has-error' : '' !!}">
	{!! Form::label('onhold_qty', 'Onhold Quantity', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('onhold_qty', null, ['class'=>'form-control']) !!}
		{!! $errors->first('onhold_qty', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('minimal_qty') ? 'has-error' : '' !!}">
	{!! Form::label('minimal_qty', 'Minimal Quantity', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('minimal_qty', null, ['class'=>'form-control']) !!}
		{!! $errors->first('minimal_qty', '<p class="help-block">:message</p>') !!}
	</div>
</div>