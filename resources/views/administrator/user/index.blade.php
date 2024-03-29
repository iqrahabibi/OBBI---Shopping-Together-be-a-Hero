@extends('layouts.app')

@section('styles')
<link href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="page-head">
	<div class="page-title">
		<h1>Menu Master User Office</h1>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="portlet light">
			<div class="portlet-title">
				<div class="caption font-green">
					<span class="caption-subject bold uppercase">
						<a href="{{ route('user.create') }}" class="btn green-haze">User Office Baru</a>
						{!! Form::select('roles',['All'=>'All']+App\Model\Role::pluck('name','name')->all(), null,['class'=>'form-control', 'placeholder'=>'Pilih Role']) !!}
					</span>
				</div>
			</div>
			<div class="portlet-body">
				{!! $html->table(['class'=>'table table-striped table-bordered dt-responsive nowrap', 'cellspacing'=>'0',  'width'=>'100%']) !!}
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
{!! $html->scripts() !!}

<script>
	$(function(){
		$('select[name="roles"]').change(function(){
			{{ $link = url('/user/') }}
			window.location.href = "{{ $link }}?roles="+$('select[name="roles"]').val()
		});
	});

	$(document.body).on('click', '.revoke-user', function (event) {
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

    $(document.body).on('click', '.ubah-status', function (event) {
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
			cancelButtonText: "Tidak",
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