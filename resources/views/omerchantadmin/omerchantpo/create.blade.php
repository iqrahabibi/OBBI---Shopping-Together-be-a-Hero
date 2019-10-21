@extends('layouts.app')

@section('content')
<div class="page-head">
	<div class="page-title">
		<h1>Menu Purchasing Order</h1>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="portlet light">
			<div class="portlet-title">
				<div class="caption font-green">
					<span class="caption-subject bold uppercase">Tambah Purchasing Order</span>
				</div>
			</div>
			<div class="portlet-body form">
				{!! Form::open(['route' => 'omerchantpo.store', 'class'=>'form-horizontal margin-bottom-40', 'role'=>'form'])!!}
					<div class="form-body">
						@include('omerchantadmin.omerchantpo._form')
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