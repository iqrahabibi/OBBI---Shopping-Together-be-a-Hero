@if(!empty($current))
<div class="form-group {!! $errors->has('current') ? 'has-error' : '' !!}">
	{!! Form::label('current', 'Lokasi saat ini', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('current', $current, ['class'=>'form-control', 'readonly']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('current', '<p class="help-block">:message</p>') !!}
	</div>
</div>
@endif
<div class="form-group {!! $errors->has('provinsi_id') ? 'has-error' : '' !!}">
	{!! Form::label('provinsi_id', 'Provinsi', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::select('provinsi_id',[''=>'']+App\Model\Provinsi::pluck('nama_provinsi','id')->all(), null,
			['class'=>'js-selectize', 'placeholder'=>'Pilih Provinsi']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('provinsi_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('kota_id') ? 'has-error' : '' !!}">
	{!! Form::label('kota_id', 'Kabupaten / Kota', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::select('kota_id',[''=>''], null,['class'=>'js-selectize', 'placeholder'=>'Pilih kota / Kota']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('kota_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('kecamatan_id') ? 'has-error' : '' !!}">
	{!! Form::label('kecamatan_id', 'Kecamatan', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::select('kecamatan_id',[''=>''], null,['class'=>'js-selectize', 'placeholder'=>'Pilih Kecamatan']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('kecamatan_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('kelurahan_id') ? 'has-error' : '' !!}">
	{!! Form::label('kelurahan_id', 'Kelurahan', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::select('kelurahan_id',[''=>''], null,['class'=>'js-selectize', 'placeholder'=>'Pilih Kelurahan']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('kelurahan_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('tipe_donasi_id') ? 'has-error' : '' !!}">
	{!! Form::label('tipe_donasi_id', 'Tipe Donasi', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::select('tipe_donasi_id',[''=>'']+App\Model\TipeDonasi::pluck('nama_tipe_donasi','id')->all(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih Tipe Donasi']) !!}
		{!! $errors->first('tipe_donasi_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('agama_id') ? 'has-error' : '' !!}">
	{!! Form::label('agama_id', 'Target Agama', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::select('agama_id',[''=>'']+App\Model\Agama::pluck('nama_agama','id')->all(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih Target Agama']) !!}
		{!! $errors->first('agama_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('nama_target_donasi') ? 'has-error' : '' !!}">
	{!! Form::label('nama_target_donasi', 'Nama Target Donasi', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('nama_target_donasi', null, ['class'=>'form-control']) !!}
		{!! $errors->first('nama_target_donasi', '<p class="help-block">:message</p>') !!}
	</div>
</div>

@section('scripts')
<script>
$(function(){
	$('select[name="provinsi_id"]').change(function(){
		{{ $link = url('/kota/data/') }}
		$.ajax({
			url: "{{ $link }}/"+$('select[name="provinsi_id"]').val(),
			type: 'GET',
			success: function(respon) {
				var selectize_data = $("#kota_id")[0].selectize;
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
	
	$('select[name="kota_id"]').change(function(){
		{{ $link = url('/kecamatan/data/') }}
		$.ajax({
			url: "{{ $link }}/"+$('select[name="kota_id"]').val(),
			type: 'GET',
			success: function(respon) {
				var selectize_data = $("#kecamatan_id")[0].selectize;
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
	
	$('select[name="kecamatan_id"]').change(function(){
		{{ $link = url('/kelurahan/data/') }}
		$.ajax({
			url: "{{ $link }}/"+$('select[name="kecamatan_id"]').val(),
			type: 'GET',
			success: function(respon) {
				var selectize_data = $("#kelurahan_id")[0].selectize;
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
