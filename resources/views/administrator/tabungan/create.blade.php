@extends('layouts.app')

@section('content')
<div class="page-head">
	<div class="page-title">
		<h1>Menu Tabungan</h1>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="portlet light">
			<div class="portlet-title">
				<div class="caption font-green">
					<span class="caption-subject bold uppercase">Tambah Tabungan</span>
				</div>
			</div>
			<div class="portlet-body form">
				{!! Form::open(['route' => 'tabungan.store', 'class'=>'form-horizontal margin-bottom-40', 'role'=>'form'])!!}
					<div class="form-body">
						@include('administrator.tabungan._form')
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
		$('select[name="o_merchant_id"]').change(function(){
			{{ $link = url('/tabungan/usahaomerchant/') }}
			$.ajax({
				url: "{{ $link }}/"+$('select[name="o_merchant_id"]').val(),
				type: 'GET',
				success: function(respon) {
					var selectize_data = $("#usaha_o_merchant_id")[0].selectize;
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