<div class="form-group {!! $errors->has('tanggal') ? 'has-error' : '' !!}">
	{!! Form::label('tanggal', 'Tanggal') !!}
	{!! Form::date('tanggal', null, ['class'=>'form-control']) !!}
	{!! $errors->first('tanggal', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {!! $errors->has('keterangan') ? 'has-error' : '' !!}">
	{!! Form::label('keterangan', 'Keterangan') !!}
	{!! Form::text('keterangan', null, ['class'=>'form-control']) !!}
	{!! $errors->first('keterangan', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {!! $errors->has('type') ? 'has-error' : '' !!}">
	{!! Form::label('type', 'Tipe Opname') !!}
	{!! Form::select('type',[''=>'','Adjustment'=>'Adjustment','Write Off'=>'Write Off'], null,['class'=>'js-selectize', 'placeholder'=>'Pilih Type']) !!}
	{!! $errors->first('type', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {!! $errors->has('barang_stok_id') ? 'has-error' : '' !!}" hidden="hidden" id="form-barang">
	{!! Form::label('barang_stok_id', 'Barang Stok') !!}
	{!! Form::select('barang_stok_id',[''=>'']+App\Model\BarangStok::listopname(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih Barang Stok']) !!}
	{!! $errors->first('barang_stok_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {!! $errors->has('jumlah') ? 'has-error' : '' !!}" hidden="hidden" id="form-jumlah">
	{!! Form::label('jumlah', 'Jumlah') !!}
	{!! Form::text('jumlah', null, ['class'=>'form-control']) !!}
	{!! $errors->first('jumlah', '<p class="help-block">:message</p>') !!}
</div>

@section('scripts')
<script>
$(document).ready(function () {
	$("#type").change(function() {
		if($("#type").val() != ''){
			$("#form-barang").show();
			$("#form-jumlah").show();
		}else{
			$("#form-barang").hide();
			$("#form-jumlah").hide();
		}
	});
});
</script>
@endsection