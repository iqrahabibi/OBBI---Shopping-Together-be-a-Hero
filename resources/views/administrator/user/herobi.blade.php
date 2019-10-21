@extends('layouts.app')

@section('content')
@if(isset($data->herobi))
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">Data User</h3>
		</div>

		<div class="panel-body">
			{!! Form::model($data) !!}
				<div class="form-group">
					{!! Form::label('fullname', 'Fullname') !!}
					{!! Form::text('fullname', $data->fullname, ['class'=>'form-control', 'disabled'=>'disabled']) !!}
				</div>
				<div class="form-group">
					{!! Form::label('email', 'Email') !!}
					{!! Form::text('email', $data->email, ['class'=>'form-control', 'disabled'=>'disabled']) !!}
				</div>
			{!! Form::close() !!}
		</div>
	</div>
		
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">Data Herobi</h3>
		</div>

		<div class="panel-body">
			<div class="form-group">
				{!! Form::label('ktp', 'Lampiran KTP') !!}<br>
				{!! $data->setupDocument($data->herobi->ktp) !!}
			</div>
			<div class="form-group">
				{!! Form::label('kk', 'Lampiran KK') !!}<br>
				{!! $data->setupDocument($data->herobi->kk) !!}
			</div>
			<div class="form-group">
				{!! Form::label('selfi', 'Lampiran Selfi') !!}<br>
				{!! $data->setupDocument($data->herobi->selfi) !!}
			</div>
		</div>
	</div>
		
	<div class="panel panel-default">
		<div class="panel-body">
			{!! Form::model($data,['url'=>$approve_url,'method'=>'post']) !!}
				{!! Form::submit('Approve', ['class'=>'btn btn-primary approve-user-herobi','data-confirm-message'=>$approve_message]) !!}
			{!! Form::close() !!}
			{!! Form::model($data,['url'=>$deny_url,'method'=>'post']) !!}
				{!! Form::submit('Deny', ['class'=>'btn btn-danger deny-user-herobi','data-confirm-message'=>$deny_message]) !!}
			{!! Form::close() !!}
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
</script>
@endsection
