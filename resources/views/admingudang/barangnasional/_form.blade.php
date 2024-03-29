<div class="form-group {!! $errors->has('barang_id') ? 'has-error' : '' !!}">
	{!! Form::label('barang_id', 'Barang *)', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		@if(empty($data))
			{!! Form::select('barang_id',[''=>'']+App\Model\Barang::list(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih Barang','disabled'=>isset($data->barang_id) ? 'disabled' : null]) !!}
		@else
			{!! Form::text('nama_barang', $data->barang->nama_barang,['class'=>'form-control','readonly']) !!}
			{!! Form::hidden('barang_id', $data->barang_id,['class' => 'form-control']) !!}
		@endif
		{!! $errors->first('barang_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('varian_id') ? 'has-error' : '' !!}">
	{!! Form::label('varian_id', 'Varian Barang *)', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		@if(empty($data))
			{!! Form::select('varian_id',[''=>'']+App\Model\BarangVarian::pluck('varian_barang','id')->all(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih Varian','disabled'=>isset($data->varian_id) ? 'disabled' : null]) !!}
		@elseif(empty($data->varian))
			{!! Form::text('varian_barang', '-',['class'=>'form-control','readonly']) !!}
		@else
			{!! Form::text('varian_barang', $data->varian->varian_barang,['class'=>'form-control','readonly']) !!}
			{!! Form::hidden('varian_id', $data->varian_id,['class' => 'form-control']) !!}
		@endif
		{!! $errors->first('varian_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('harga_beli') ? 'has-error' : '' !!}">
	{!! Form::label('harga_beli', 'Harga Beli', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('harga_beli', null, ['class'=>'form-control']) !!}
		{!! $errors->first('harga_beli', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('qty') ? 'has-error' : '' !!}">
	{!! Form::label('qty', 'Quantity *)', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('qty', null, ['class'=>'form-control']) !!}
		{!! $errors->first('qty', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('harga_jual') ? 'has-error' : '' !!}">
	{!! Form::label('harga_jual', 'Harga Jual *)', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('harga_jual', null, ['class'=>'form-control']) !!}
		{!! $errors->first('harga_jual', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('urut') ? 'has-error' : '' !!}">
	{!! Form::label('urut', 'Urutan', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('urut', null, ['class'=>'form-control']) !!}
		{!! $errors->first('urut', '<p class="help-block">:message</p>') !!}
	</div>
</div>

@section('scripts')
<script>
$(function(){
    $('select[name="barang_id"]').change(function(){
        {{ $link = url('/admingudang/barangvarian/data/') }}
        $.ajax({
            url: "{{ $link }}/"+$('select[name="barang_id"]').val(),
            type: 'GET',
            success: function(respon) {
                var selectize_data = $("#varian_id")[0].selectize;
                    selectize_data.clearOptions();
                var data = jQuery.parseJSON(respon);
                for (var i = 0; i < data.hasil.length; i++) {
                    selectize_data.addOption({
                        text:data.hasil[i].nama,
                        value:data.hasil[i].id
                    });
                    selectize_data.refreshOptions() ;
                }
            },
        });
    });

});
</script>
@endsection
