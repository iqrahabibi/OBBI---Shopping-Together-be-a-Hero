<div class="form-group {!! $errors->has('gudang_id') ? 'has-error' : '' !!}">
	{!! Form::label('gudang_id', 'Gudang') !!}
	{!! Form::text('gudang_id', $data->gudang->nama_gudang, ['class'=>'form-control', 'readonly']) !!}
	{!! $errors->first('gudang_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {!! $errors->has('tanggal') ? 'has-error' : '' !!}">
	{!! Form::label('tanggal', 'Tanggal Purchasing Order') !!}
	{!! Form::date('tanggal', $data->tanggal, ['class'=>'form-control', 'readonly']) !!}
	{!! $errors->first('tanggal', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {!! $errors->has('tanggal_po_masuk') ? 'has-error' : '' !!}">
	{!! Form::label('tanggal_po_masuk', 'Tanggal Masuk') !!}
	{!! Form::date('tanggal_po_masuk', null, ['class'=>'form-control']) !!}
	{!! $errors->first('tanggal_po_masuk', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {!! $errors->has('tanggal_batas_retur') ? 'has-error' : '' !!}">
	{!! Form::label('tanggal_batas_retur', 'Tanggal Batas Retur') !!}
	{!! Form::date('tanggal_batas_retur', null, ['class'=>'form-control']) !!}
	{!! $errors->first('tanggal_batas_retur', '<p class="help-block">:message</p>') !!}
</div>

<div class="form-group {!! $errors->has('barang') ? 'has-error' : '' !!}">
	{!! Form::label('detil', 'Detail Purchasing Order') !!}
	@foreach($data->o_merchant_po_detail as $detil)
		<div class="form-group">
			<div class="panel panel-default">
				<div class="panel-body">
					{!! Form::label('barang_grosir', 'Barang') !!}
					{!! Form::text('barang_grosir_nama', $detil->barang_grosir->barang->nama($detil->barang_grosir->barang->nama_barang), ['class'=>'form-control', 'readonly']) !!}
					{!! Form::hidden('barang_grosir[]', $detil->barang_id, ['class'=>'form-control', 'readonly']) !!}

					{!! Form::label('varian_barang', 'Varian') !!}
					{!! Form::text('varian_barang', $detil->barang_grosir->varian->varian_barang . ' ( ' . $detil->barang_grosir->harga_jual . '/pcs )', ['class'=>'form-control', 'readonly']) !!}
					{!! Form::hidden('varian[]', $detil->barang_id, ['class'=>'form-control', 'readonly']) !!}
					
					{!! Form::label('jumlah', 'Jumlah') !!}
					{!! Form::text('jumlah[]', $detil->jumlah, ['class'=>'form-control', 'readonly']) !!}
					
					{!! Form::label('harga', 'Harga') !!}
					{!! Form::text('harga[]', $detil->harga, ['class'=>'form-control', 'readonly']) !!}
					
					{!! Form::label('jumlah_terima', 'Jumlah yang Diterima') !!}
					{!! Form::text('jumlah_masuk[]', null, ['class'=>'form-control', 'placeholder' => 'Input Jumlah yang Diterima']) !!}
					
					{!! Form::label('harga_terima', 'Harga yang Diterima') !!}
					{!! Form::text('harga_masuk[]', null, ['class'=>'form-control', 'placeholder' => 'Input Harga yang Diterima']) !!}
					
					{!! Form::label('jumlah_retur', 'Jumlah yang Diretur') !!}
					{!! Form::text('jumlah_retur[]', null, ['class'=>'form-control', 'placeholder' => 'Input Jumlah yang Diretur']) !!}

					{!! Form::hidden('po_id[]', $detil->id, ['class'=>'form-control', 'readonly']) !!}
				</div>
			</div>
		</div>
	@endforeach
</div>
