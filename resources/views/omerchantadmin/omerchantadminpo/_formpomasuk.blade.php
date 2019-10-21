<div class="form-group {!! $errors->has('gudang_id') ? 'has-error' : '' !!}">
	{!! Form::label('gudang_id', 'Gudang', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('gudang_id', $data->gudang->nama_gudang, ['class'=>'form-control', 'readonly']) !!}
		{!! $errors->first('gudang_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('no_faktur') ? 'has-error' : '' !!}">
	{!! Form::label('no_faktur', 'Nomor Faktur', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('no_faktur', null, ['class'=>'form-control']) !!}
		{!! $errors->first('no_faktur', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('tanggal') ? 'has-error' : '' !!}">
	{!! Form::label('tanggal', 'Tanggal Purchasing Order', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::date('tanggal', $data->tanggal, ['class'=>'form-control', 'readonly']) !!}
		{!! $errors->first('tanggal', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('tanggal_po_masuk') ? 'has-error' : '' !!}">
	{!! Form::label('tanggal_po_masuk', 'Tanggal Masuk', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::date('tanggal_po_masuk', null, ['class'=>'form-control']) !!}
		{!! $errors->first('tanggal_po_masuk', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('tanggal_batas_retur') ? 'has-error' : '' !!}">
	{!! Form::label('tanggal_batas_retur', 'Tanggal Batas Retur', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::date('tanggal_batas_retur', null, ['class'=>'form-control']) !!}
		{!! $errors->first('tanggal_batas_retur', '<p class="help-block">:message</p>') !!}
	</div>
</div>

<div class="form-group {!! $errors->has('barang') ? 'has-error' : '' !!}">
	{!! Form::label('detil', 'Detail Purchasing Order') !!}
	<?php $nomorListBarang = 1; ?>
	@foreach($data->o_merchant_po_detail as $detil)
		<div class="form-group">
			<div class="panel panel-default">
				<div class="panel-body">
					{!! Form::label('barang_grosir', 'Barang', ['class'=>'col-md-2 control-label']) !!}
					<div class="col-md-10">
						{!! Form::text('barang_grosir_nama', $detil->barang_grosir->barang->nama($detil->barang_grosir->barang->nama_barang), ['class'=>'form-control', 'readonly']) !!}
						{!! Form::hidden('barang_grosir[]', $detil->barang_grosir_id, ['class'=>'form-control', 'readonly']) !!}
					</div>

					{!! Form::label('varian_barang', 'Varian', ['class'=>'col-md-2 control-label']) !!}
					<div class="col-md-10">
						{!! Form::text('varian_barang', $detil->barang_grosir->varian->varian_barang . ' ( ' . $detil->barang_grosir->harga_jual . '/pcs )', ['class'=>'form-control', 'readonly']) !!}
						{!! Form::hidden('varian[]', $detil->barang_id, ['class'=>'form-control', 'readonly']) !!}
					</div>

					{!! Form::label('jumlah', 'Jumlah', ['class'=>'col-md-2 control-label']) !!}
					<div class="col-md-10">
						{!! Form::text('jumlah[]', $detil->jumlah, ['class'=>'form-control', 'readonly']) !!}
					</div>

					{!! Form::label('harga', 'Harga', ['class'=>'col-md-2 control-label']) !!}
					<div class="col-md-10">
						{!! Form::text('harga[]', $detil->rupiah($detil->harga), ['class'=>'form-control', 'readonly']) !!}
					</div>

					{!! Form::label('jumlah_terima', 'Jumlah yang Diterima', ['class'=>'col-md-2 control-label']) !!}
					<div class="col-md-10">
						{!! Form::text('jumlah_masuk[]', $detil->jumlah, ['class'=>'form-control', 'id'=>"ListJumlahBarang$nomorListBarang", 'onkeyup'=>"updateHarga($nomorListBarang,$detil->jumlah,$detil->harga)"]) !!}
					</div>

					{!! Form::label('harga_terima', 'Harga yang Diterima', ['class'=>'col-md-2 control-label']) !!}
					<div class="col-md-10">
						{!! Form::text('harga_masuk[]', $detil->rupiah($detil->harga), ['class'=>'form-control', 'readonly', 'id'=>"ListHargaBarang$nomorListBarang"]) !!}
					</div>

					{!! Form::label('jumlah_retur', 'Jumlah yang Diretur', ['class'=>'col-md-2 control-label']) !!}
					<div class="col-md-10">
						{!! Form::text('jumlah_retur[]', null, ['class'=>'form-control', 'placeholder' => 'Input Jumlah yang Diretur']) !!}
					</div>

					{!! Form::hidden('po_id[]', $detil->id, ['class'=>'form-control', 'readonly']) !!}
				</div>
			</div>
		</div>
		<?php $nomorListBarang += 1; ?>
	@endforeach
</div>

@section('scripts')
<script>
function updateHarga(nomorListBarang, jumlah, harga){
	var satuan = harga / jumlah;
	var jumlahbaru = $("#ListJumlahBarang"+nomorListBarang).val();
	if(jumlahbaru>jumlah){
		alert('Jumlah melebihi pesanan.');
		$("#ListJumlahBarang"+nomorListBarang).val(jumlah);
		return;
	}
	$("#ListHargaBarang"+nomorListBarang).val(formatRupiah(jumlahbaru * satuan, 'Rp.'));
}
 
/* Fungsi formatRupiah */
function formatRupiah(angka, prefix){
	var number_string = angka.toString(),
	split   		= number_string.split(','),
	sisa     		= split[0].length % 3,
	rupiah     		= split[0].substr(0, sisa),
	ribuan     		= split[0].substr(sisa).match(/\d{3}/gi);

	// tambahkan titik jika yang di input sudah menjadi angka ribuan
	if(ribuan){
		separator = sisa ? '.' : '';
		rupiah += separator + ribuan.join('.');
	}

	rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
	return prefix == undefined ? rupiah : (rupiah ? 'Rp.' + rupiah : '');
}
</script>
@endsection
