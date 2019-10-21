@extends('layouts.app')

@section('content')
@if(isset($data->herobi))
	
		<div class="page-head">
			<div class="page-title">
				<h1>Data User</h1>
			</div>
		</div>
		<div class="panel panel-default">
		<div class="panel-body">
			{!! Form::model($data) !!}
				<div class="form-group">
					{!! Form::label('fullname', 'Fullname', ['class'=>'col-md-2 control-label']) !!}
					<div class="col-md-10">
						{!! Form::text('fullname', $data->fullname, ['class'=>'form-control', 'disabled'=>'disabled']) !!}
					</div>
				</div>
				<div class="form-group">
					{!! Form::label('email', 'Email',['class'=>'col-md-2 control-label']) !!}
					<div class="col-md-10">
						{!! Form::text('email', $data->email, ['class'=>'form-control', 'disabled'=>'disabled']) !!}
					</div>
				</div>
			{!! Form::close() !!}
		</div>
	</div>
	
		<div class="page-head">
				<div class="page-title">
					<h1>Data Herobi</h1>
				</div>
			</div>
		<div class="panel panel-default">
		<div class="panel-body">
			<div class="form-group">
				{!! Form::label('ktp', 'Lampiran KTP') !!}<br>
				{!! $gambar->setup($data->herobi->ktp) !!}
			</div>
			<div class="form-group">
				{!! Form::label('kk', 'Lampiran KK') !!}<br>
				{!! $gambar->setup($data->herobi->kk) !!}
			</div>
			<div class="form-group">
				{!! Form::label('selfi', 'Lampiran Selfi') !!}<br>
				{!! $gambar->setup($data->herobi->selfi) !!}
			</div>
		</div>
	</div>
		
		<div class="page-head">
				<div class="page-title">
					<h1>Data KTP User</h1>
				</div>
			</div>
		<div class="panel panel-default">
		<div class="panel-body">
			{!! Form::model($data,['url'=>$approve_url,'method'=>'post']) !!}
				<!-- Data Detail User -->
				<div class="form-group {!! $errors->has('detail_alamat') ? 'has-error' : '' !!}">
					{!! Form::label('detail_alamat', 'Alamat KTP', ['class'=>'col-md-2 control-label', 'placeholder'=>'Alamat Lengkat sesuai KTP']) !!}
					<div class="col-md-10">
						{!! Form::text('detail_alamat', isset($data->detail) ? $data->detail->alamat : null, ['class'=>'form-control']) !!}
						{!! $errors->first('detail_alamat', '<p class="help-block">:message</p>') !!}
					</div>
				</div>
				<div class="form-group {!! $errors->has('nik') ? 'has-error' : '' !!}">
					{!! Form::label('nik', 'Nomor KTP (NIK)', ['class'=>'col-md-2 control-label', 'placeholder'=>'NIK sesuai KTP']) !!}
					<div class="col-md-10">
						{!! Form::text('nik', isset($data->herobi) ? $data->herobi->nik : null, ['class'=>'form-control']) !!}
						{!! $errors->first('nik', '<p class="help-block">:message</p>') !!}
					</div>
				</div>
				<div class="form-group {!! $errors->has('agama_id') ? 'has-error' : '' !!}">
					{!! Form::label('agama_id', 'Agama', ['class'=>'col-md-2 control-label']) !!}
					<div class="col-md-10">
						{!! Form::select('agama_id',[''=>'']+App\Model\Agama::pluck('nama_agama','id')->all(), 
							isset($$data->detail->agama->nama_agama) ? $data->detail->agama->nama_agama : null,
							['class'=>'js-selectize', 'placeholder'=>'Pilih Agama']) !!}
						{!! $errors->first('agama_id', '<p class="help-block">:message</p>') !!}
					</div>
				</div>
				<div class="form-group {!! $errors->has('phone') ? 'has-error' : '' !!}">
					{!! Form::label('phone', 'Phone', ['class'=>'col-md-2 control-label', 'placeholder'=>'Nomor Handphone / Telpon']) !!}
					<div class="col-md-10">
						{!! Form::text('phone', isset($data->detail) ? $data->detail->phone : '', ['class'=>'form-control']) !!}
						{!! $errors->first('phone', '<p class="help-block">:message</p>') !!}
					</div>
				</div>
				<div class="form-group {!! $errors->has('provinsi_id') ? 'has-error' : '' !!}">
					{!! Form::label('provinsi_id', 'Provinsi', ['class'=>'col-md-2 control-label']) !!}
					<div class="col-md-10">
						{!! Form::select('provinsi_id',[''=>'']+App\Model\Provinsi::pluck('nama_provinsi','id')->all(), null,
							['class'=>'js-selectize', 'placeholder'=>'Pilih Provinsi']) !!}
						{!! $errors->first('provinsi_id', '<p class="help-block">:message</p>') !!}
					</div>
				</div>
				<div class="form-group {!! $errors->has('kota_id') ? 'has-error' : '' !!}">
					{!! Form::label('kota_id', 'Kabupaten / Kota', ['class'=>'col-md-2 control-label']) !!}
					<div class="col-md-10">
						{!! Form::select('kota_id',[''=>''], null,['class'=>'js-selectize', 'placeholder'=>'Pilih kota / Kota']) !!}
						{!! $errors->first('kota_id', '<p class="help-block">:message</p>') !!}
					</div>
				</div>
				<div class="form-group {!! $errors->has('kecamatan_id') ? 'has-error' : '' !!}">
					{!! Form::label('kecamatan_id', 'Kecamatan', ['class'=>'col-md-2 control-label']) !!}
					<div class="col-md-10">
						{!! Form::select('kecamatan_id',[''=>''], null,['class'=>'js-selectize', 'placeholder'=>'Pilih Kecamatan']) !!}
						{!! $errors->first('kecamatan_id', '<p class="help-block">:message</p>') !!}
					</div>
				</div>
				<div class="form-group {!! $errors->has('kelurahan_id') ? 'has-error' : '' !!}">
					{!! Form::label('kelurahan_id', 'Kelurahan', ['class'=>'col-md-2 control-label']) !!}
					<div class="col-md-10">
						{!! Form::select('kelurahan_id',[''=>''], null,['class'=>'js-selectize', 'placeholder'=>'Pilih Kelurahan']) !!}
						{!! $errors->first('kelurahan_id', '<p class="help-block">:message</p>') !!}
					</div>
				</div>
				<div class="form-group">
					{!! Form::submit('Approve', ['class'=>'btn btn-primary approve-user-herobi','data-confirm-message'=>$approve_message]) !!}
				</div>
			{!! Form::close() !!}
			<div class="form-group">
				{!! Form::model($data,['url'=>$deny_url,'method'=>'post']) !!}
					<div class="form-group {!! $errors->has('notes') ? 'has-error' : '' !!}">
						{!! Form::label('notes', 'Catatan', ['class'=>'col-md-2 control-label']) !!}
						<div class="col-md-10">
							{!! Form::text('notes', '', ['class'=>'form-control', 'placeholder'=>'Catatan Penolakan']) !!}
							{!! $errors->first('notes', '<p class="help-block">:message</p>') !!}
						</div>
					</div>
					{!! Form::submit('Deny', ['class'=>'btn btn-danger deny-user-herobi','data-confirm-message'=>$deny_message]) !!}
				{!! Form::close() !!}
			</div>
		</div>
	</div>
@else
	<div class="panel-heading">
		<h3 class="panel-title">{!! Form::label('not_found', 'Data Not Found') !!}</h3>
	</div>
@endif
@endsection

@section('scripts')
<script>
	$(document.body).on('click', '.approve-user-herobi', function (event) {
		event.preventDefault();
		var $form = $(this).closest('form');
		var $el = $(this);
		var text = $el.data('confirm-message') ? $el.data('confirm-message') : 'Apa kamu yakin ?';

		swal({
			title : "",
			text : text,
			type : "warning",
			showCancelButton: true,					
			confirmButtonText: "Ya",
			cancelButtonText: "Batal",
			allowOutsideClick: false,
			reverseButtons: true,
			buttonsStyling: true
		}).then((result) => {
			if (result.value) {
				$form.submit();
			}
		});
	});
    
	$(document.body).on('click', '.deny-user-herobi', function (event) {
		event.preventDefault();
		var $form = $(this).closest('form');
		var $el = $(this);
		var text = $el.data('confirm-message') ? $el.data('confirm-message') : 'Apa kamu yakin ?';

		swal({
			title : "",
			text : text,
			type : "warning",
			showCancelButton: true,					
			confirmButtonText: "Ya",
			cancelButtonText: "Batal",
			allowOutsideClick: false,
			reverseButtons: true,
			buttonsStyling: true
		}).then((result) => {
			if (result.value) {
				$form.submit();
			}
		});
	});

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