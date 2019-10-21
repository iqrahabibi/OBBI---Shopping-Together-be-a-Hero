@extends('layouts.app')

@section('content')
<div class="page-head">
	<div class="page-title">
		<h1>Menu Kelurahan</h1>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="portlet light">
			<div class="portlet-title">
				<div class="caption font-green">
					<span class="caption-subject bold uppercase">Tambah Kelurahan</span>
				</div>
			</div>
			<div class="portlet-body form">
				{!! Form::open(['route' => 'kelurahan.store', 'class'=>'form-horizontal margin-bottom-40', 'role'=>'form'])!!}
					<div class="form-body">
						<div class="form-group {!! $errors->has('provinsi_id') ? 'has-error' : '' !!}">
							{!! Form::label('provinsi_id', 'Provinsi', ['class'=>'col-md-2 control-label']) !!}
							<div class="col-md-10">
							{!! Form::select('provinsi_id',[''=>'']+App\Model\Provinsi::pluck('nama_provinsi','id')->all(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih Provinsi']) !!}
								<div class="form-control-focus"></div>
								{!! $errors->first('provinsi_id', '<p class="help-block">:message</p>') !!}
							</div>
						</div>
						<div class="form-group {!! $errors->has('kota_id') ? 'has-error' : '' !!}">
							{!! Form::label('kota_id', 'Kota', ['class'=>'col-md-2 control-label']) !!}
							<div class="col-md-10">
								{!! Form::select('kota_id',[''=>''], null,['class'=>'js-selectize', 'placeholder'=>'Pilih Kota']) !!}
								<div class="form-control-focus"></div>
								{!! $errors->first('kota_id', '<p class="help-block">:message</p>') !!}
							</div>
						</div>

						@include('administrator.kelurahan._form')
					</div>
					<div class="form-actions">
						<div class="row">
							<div class="col-md-offset-2 col-md-10">
								{!! Form::submit(isset($data) ? 'Update' : 'Save', ['class'=>'btn green-haze']) !!}
							</div>
						</div>
					</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>
@endsection

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
