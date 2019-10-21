<div class="form-group {!! $errors->has('suplier_id') ? 'has-error' : '' !!}">
	{!! Form::label('suplier_id', 'Nama Suplier', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
        @if(empty($data))
            {!! Form::select('suplier_id',[''=>'']+App\Model\Suplier::pluck('nama_suplier','id')->all(), null, ['class'=>'js-selectize', 'placeholder'=>'Pilih Suplier', 'disabled'=>isset($data->suplier_id) ? 'disabled' : null]) !!}
        @else
            {!! Form::text('nama_suplier',$data->suplier->nama_suplier,['class' => 'form-control','readonly' => true]) !!}
            {!! Form::hidden('suplier_id',$data->suplier_id) !!}
        @endif
		<div class="form-control-focus"></div>
		{!! $errors->first('suplier_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('barang_id') ? 'has-error' : '' !!}">
	{!! Form::label('barang_id', 'Nama Barang', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
        @if(empty($data))
            {!! Form::select('barang_id',[''=>'']+App\Model\Barang::list(), null, ['class'=>'js-selectize', 'placeholder'=>'Pilih Barang', 'disabled'=>isset($data->barang_id) ? 'disabled' : null]) !!}
        @else
            {!! Form::text('nama_barang', $data->barang->nama_barang, ['class'=>'form-control','readonly' => true]) !!}
            {!! Form::hidden('barang_id',$data->barang_id) !!}
        @endif
		<div class="form-control-focus"></div>
		{!! $errors->first('barang_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('harga_beli') ? 'has-error' : '' !!}">
	{!! Form::label('harga_beli', 'Harga Beli', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('harga_beli', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('harga_beli', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('urut') ? 'has-error' : '' !!}">
	{!! Form::label('urut', 'Nomor Urut', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('urut', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('urut', '<p class="help-block">:message</p>') !!}
	</div>
</div>