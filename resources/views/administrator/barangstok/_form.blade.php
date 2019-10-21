<div class="form-group {!! $errors->has('parent_id') ? 'has-error' : '' !!}">
	{!! Form::label('parent_id', 'Parent Barang Stok') !!}
	{!! Form::select('parent_id',[''=>'']+App\Model\BarangStok::list(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih Parent Stok']) !!}
	{!! $errors->first('parent_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {!! $errors->has('barang_id') ? 'has-error' : '' !!}">
	{!! Form::label('barang_id', 'Barang') !!}
	{!! Form::select('barang_id',[''=>'']+App\Model\Barang::list(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih Barang']) !!}
	{!! $errors->first('barang_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {!! $errors->has('barang_conversi_id') ? 'has-error' : '' !!}">
	{!! Form::label('barang_conversi_id', 'Barang Conversi') !!}
	{!! Form::select('barang_conversi_id',[''=>'']+App\Model\BarangConversi::list(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih Barang Conversi']) !!}
	{!! $errors->first('barang_conversi_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {!! $errors->has('gudang_id') ? 'has-error' : '' !!}">
	{!! Form::label('gudang_id', 'Gudang') !!}
	{!! Form::select('gudang_id',[''=>'']+App\Model\Gudang::pluck('nama_gudang', 'id')->all(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih Gudang']) !!}
	{!! $errors->first('gudang_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {!! $errors->has('jumlah') ? 'has-error' : '' !!}">
	{!! Form::label('jumlah', 'Jumlah') !!}
	{!! Form::text('jumlah', null, ['class'=>'form-control']) !!}
	{!! $errors->first('jumlah', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {!! $errors->has('harga_satuan') ? 'has-error' : '' !!}">
	{!! Form::label('harga_satuan', 'Harga Satuan') !!}
	{!! Form::text('harga_satuan', null, ['class'=>'form-control']) !!}
	{!! $errors->first('harga_satuan', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {!! $errors->has('periode') ? 'has-error' : '' !!}">
	{!! Form::label('periode', 'Periode') !!}
	{!! Form::text('periode', null, ['class'=>'form-control']) !!}
	{!! $errors->first('periode', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {!! $errors->has('publish') ? 'has-error' : '' !!}">
	{!! Form::label('publish', 'Publish') !!}
	<p class="form-control">{!! Form::radio('publish', 1, isset($data) && $data->publish ? true : false, ['id'=>'button-publish-ya']) !!} 
		{!! Form::label('label_level_ya', 'Ya', ['id'=>'label-publish-ya']) !!}
	</p>
	<p class="form-control">{!! Form::radio('publish', 0, true, ['id'=>'button-publish-tidak']) !!} 
		{!! Form::label('label_level_tidak', 'Tidak', ['id'=>'label-publish-tidak']) !!}
	</p>
	{!! $errors->first('publish', '<p class="help-block">:message</p>') !!}
</div>

@section('scripts')
<script>
$(document).ready(function () {
	$("#label-publish-ya").click(function () {
        $("#button-publish-ya").filter('[value=1]').prop('checked', true);
	});
	$("#label-publish-tidak").click(function () {
        $("#button-publish-tidak").filter('[value=0]').prop('checked', true);
	});
});
</script>
@endsection