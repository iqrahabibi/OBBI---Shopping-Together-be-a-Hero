<div class="form-group {!! $errors->has('om_po_id') ? 'has-error' : '' !!}">
	{!! Form::label('om_po_id', 'OMerchant Purchasing Order', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('om_po_id', $data->omerchant->nama_omerchant, ['class'=>'form-control', 'readonly']) !!}
		{!! $errors->first('om_po_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('tanggal_po') ? 'has-error' : '' !!}">
	{!! Form::label('tanggal_po', 'Tanggal Purchasing Order', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::date('tanggal_po', $data->tanggal, ['class'=>'form-control', 'readonly']) !!}
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

<div class="form-group {!! $errors->has('barang') ? 'has-error' : '' !!}">
	{!! Form::label('detil', 'Detail Purchasing Order') !!}
	@foreach($data->om_po_detail as $detil)
		<div class="form-group">
			<div class="panel panel-default">
				<div class="panel-body">
					{!! Form::label('barang', 'Barang', ['class'=>'col-md-2 control-label']) !!}
					<div class="col-md-10">
						{!! Form::text('nama', $detil->barangstok->barang->nama($detil->barangstok->barang->nama_barang), ['class'=>'form-control', 'readonly']) !!}
						{!! Form::hidden('barang[]', $detil->barangstok->id, ['class'=>'form-control', 'readonly']) !!}
					</div>

					{!! Form::label('barang_conversi', 'Satuan', ['class'=>'col-md-2 control-label']) !!}
					<div class="col-md-10">
						{!! Form::text('satuan', $detil->barangstok->barang_conversi->satuan, ['class'=>'form-control', 'readonly']) !!}
						{!! Form::hidden('barang_conversi[]', $detil->barangstok->barang_conversi->id, ['class'=>'form-control', 'readonly']) !!}
					</div>
					
					{!! Form::label('jumlah', 'Jumlah', ['class'=>'col-md-2 control-label']) !!}
					<div class="col-md-10">
						{!! Form::text('jumlah[]', $detil->jumlah, ['class'=>'form-control', 'readonly']) !!}
					</div>
					
					{!! Form::label('harga', 'Harga', ['class'=>'col-md-2 control-label']) !!}
					<div class="col-md-10">
						{!! Form::text('harga[]', $detil->harga, ['class'=>'form-control', 'readonly']) !!}
					</div>
					
					{!! Form::label('jumlah_terima', 'Jumlah yang Diterima', ['class'=>'col-md-2 control-label']) !!}
					<div class="col-md-10">
						{!! Form::text('jumlah_masuk[]', null, ['class'=>'form-control', 'placeholder' => 'Input Jumlah yang Diterima']) !!}
					</div>
					
					{!! Form::label('harga_terima', 'Harga yang Diterima', ['class'=>'col-md-2 control-label']) !!}
					<div class="col-md-10">
						{!! Form::text('harga_masuk[]', null, ['class'=>'form-control', 'placeholder' => 'Input Harga yang Diterima']) !!}
					</div>

					{!! Form::hidden('po_id[]', $detil->id, ['class'=>'form-control', 'readonly']) !!}
				</div>
			</div>
		</div>
	@endforeach
</div>
