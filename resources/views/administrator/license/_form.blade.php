<div class="form-group {!! $errors->has('nomor_sertifikat') ? 'has-error' : '' !!}">
	{!! Form::label('nomor_sertifikat', 'Nomor Sertifikat', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('nomor_sertifikat', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('nomor_sertifikat', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('nomor_kartu') ? 'has-error' : '' !!}">
	{!! Form::label('nomor_kartu', 'Nomor Kartu', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('nomor_kartu', null, ['class'=>'form-control']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('nomor_kartu', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('file_perjanjian') ? 'has-error' : '' !!}">
	{!! Form::label('file_perjanjian', 'File Perjanjian', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::file('file_perjanjian') !!}
		@if(!empty($data) && $data->file_perjanjian)
			<img src="storage{{$data->file_perjanjian}}" width="400" class="img-responsive">
		@endif
		<div class="form-control-focus"></div>
		{!! $errors->first('file_perjanjian', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('file_sertifikat') ? 'has-error' : '' !!}">
	{!! Form::label('file_sertifikat', 'File Sertifikat', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::file('file_sertifikat') !!}
		@if(!empty($data) && $data->file_sertifikat)
			<img src="storage{{$data->file_sertifikat}}" width="400" class="img-responsive">
		@endif
		<div class="form-control-focus"></div>
		{!! $errors->first('file_sertifikat', '<p class="help-block">:message</p>') !!}
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
