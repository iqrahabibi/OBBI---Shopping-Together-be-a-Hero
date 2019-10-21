@extends('layouts.app')

@section('content')
<div class="page-head">
	<div class="page-title">
		<h1>Menu Usaha OMerchant</h1>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="portlet light">
			<div class="portlet-title">
				<div class="caption font-green">
					<span class="caption-subject bold uppercase">Ubah Usaha OMerchant</span>
				</div>
			</div>
			<div class="portlet-body">
				{!! Form::model($data, ['route' => ['usahaomerchant.update', $data],'method' =>'patch', 'class'=>'form-horizontal margin-bottom-40', 'role'=>'form'])!!}
					<div class="form-body">
						@include('administrator.usahaomerchant._form', ['model' => $data])
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
$(document).ready(function () {
	$("#label-level-ya").click(function () {
        $("#button-level-ya").filter('[value=1]').prop('checked', true);
	});
	$("#label-level-tidak").click(function () {
        $("#button-level-tidak").filter('[value=0]').prop('checked', true);
	});
});
</script>
@endsection
