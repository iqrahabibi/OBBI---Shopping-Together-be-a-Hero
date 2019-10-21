<div class="form-group {!! $errors->has('kode_merchant') ? 'has-error' : '' !!}">
	{!! Form::label('kode_omerchant', 'Omerchant') !!}
	{!! Form::select('kode_omerchant',[''=>'']+App\Model\OMerchant::pluck('nama_omerchant','kode')->all(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih O-Merchant']) !!}
	{!! $errors->first('kode_omerchant', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {!! $errors->has('barang_id') ? 'has-error' : '' !!}">
	{!! Form::label('barang_id', 'Barang') !!}
	{!! Form::select('barang_id', [''=>'']+App\Model\Barang::pluck('nama_barang','id')->all(), null, ['class'=>'js-selectize', 'placeholder'=>'Pilih Barang']) !!}
	{!! $errors->first('barang_id', '<p class="help-block">:message</p>') !!}
</div>

<div class="form-group {!! $errors->has('barang_conversi_id') ? 'has-error' : '' !!}">
	{!! Form::label('barang_conversi_id', 'Barang Konversi') !!}
	{!! Form::select('barang_conversi_id', [''=>'']+App\Model\BarangConversi::pluck('satuan','id')->all(), null, ['class'=>'js-selectize', 'placeholder'=>'Pilih Barang Konversi']) !!}
	{!! $errors->first('barang_conversi_id', '<p class="help-block">:message</p>') !!}
</div>

<div class="form-group {!! $errors->has('jumlah') ? 'has-error' : '' !!}">
	{!! Form::label('jumlah', 'Jumlah') !!}
	{!! Form::text('jumlah', null, ['class'=>'form-control']) !!}
	{!! $errors->first('jumlah', '<p class="help-block">:message</p>') !!}
</div>

<div class="form-group {!! $errors->has('periode') ? 'has-error' : '' !!}">
	{!! Form::label('periode', 'Periode') !!}
	{!! Form::date('periode', null, ['class'=>'form-control']) !!}
	{!! $errors->first('periode', '<p class="help-block">:message</p>') !!}
</div>

<div class="form-group {!! $errors->has('harga_satuan') ? 'has-error' : '' !!}">
	{!! Form::label('harga_satuan', 'Harga Satuan') !!}
	{!! Form::text('harga_satuan', null, ['class'=>'form-control']) !!}
	{!! $errors->first('harga_satuan', '<p class="help-block">:message</p>') !!}
</div>