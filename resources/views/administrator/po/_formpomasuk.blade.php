<div class="form-group {!! $errors->has('suplier_id') ? 'has-error' : '' !!}">
	{!! Form::label('suplier_id', 'Suplier', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('gudang_id', $data->suplier->nama_suplier, ['class'=>'form-control', 'readonly']) !!}
		{!! $errors->first('suplier_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
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
<div class="form-group {!! $errors->has('tanggal_po') ? 'has-error' : '' !!}">
	{!! Form::label('tanggal_po', 'Tanggal Purchasing Order', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::date('tanggal_po', null, ['class'=>'form-control', 'readonly']) !!}
		{!! $errors->first('tanggal_po', '<p class="help-block">:message</p>') !!}
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

<div class="form-group {!! $errors->has('barang') ? 'has-error' : '' !!}" id="DataBarang">
	{!! Form::label('detil', 'Detail Purchasing Order', ['class'=>'col-md-2 control-label']) !!}
	<?php $nomorListBarang = 1; ?>
	@foreach($data->purchasing_order_detil as $detil)
		<div class="form-group">
			<div class="panel panel-default">
				<div class="panel-body">
					{!! Form::label('barang', "Barang") !!}
						{!! Form::text('nama', $detil->barang->nama($detil->barang->nama_barang), ['class'=>'form-control', 'readonly']) !!}
						{!! Form::hidden('barang[]', $detil->barang_id, ['class'=>'form-control', 'readonly']) !!}

					{!! Form::label('barang_conversi', 'Satuan') !!}
						{!! Form::text('satuan', $detil->barang_conversi->satuan, ['class'=>'form-control', 'readonly']) !!}
						{!! Form::hidden('barang_conversi[]', $detil->barang_conversi_id, ['class'=>'form-control', 'readonly']) !!}
					
					{!! Form::label('jumlah', 'Jumlah') !!}
						{!! Form::text('jumlah[]', $detil->jumlah, ['class'=>'form-control', 'readonly']) !!}
					
					{!! Form::label('harga', 'Harga') !!}
						{!! Form::text('harga[]', $detil->harga, ['class'=>'form-control', 'readonly']) !!}
					
					{!! Form::label('jumlah_terima', 'Jumlah yang Diterima') !!}
						{!! Form::text('jumlah_masuk[]', $detil->jumlah, ['class'=>'form-control', 'placeholder' => 'Input Jumlah yang Diterima', 'id'=>"ListJumlahBarang$nomorListBarang", 'onkeyup'=>"updateHarga($nomorListBarang,$detil->jumlah,$detil->harga)"]) !!}
					
					{!! Form::label('harga_terima', 'Harga yang Diterima') !!}
						{!! Form::text('harga_masuk[]', $detil->harga, ['class'=>'form-control', 'readonly', 'id'=>"ListHargaBarang$nomorListBarang"]) !!}

						{!! Form::hidden('po_id[]', $detil->id, ['class'=>'form-control', 'readonly']) !!}


					{!! Form::label('jumlah', 'Jumlah Retur') !!}
						{!! Form::number('jumlah_retur[]', null, ['class'=>'form-control']) !!}
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
	$("#ListHargaBarang"+nomorListBarang).val(formatRupiah(jumlahbaru*satuan, 'Rp.'));
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
