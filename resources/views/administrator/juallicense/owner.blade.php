@extends('layouts.app')

@section('content')
@if(isset($data))
	<div class="page-head">
		<div class="page-title">
			<h1>Menu Jual License</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="portlet light">
				<div class="portlet-title">
					<div class="caption font-green">
						<span class="caption-subject bold uppercase">Data Detail License Owner</span>
					</div>
				</div>
				<div class="portlet-body form">
					{!! Form::open(['route' => 'juallicense.store', 'class'=>'form-horizontal margin-bottom-40', 'role'=>'form'])!!}
						<div class="form-body">
							@include('administrator.juallicense._form_owner')
						</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
@else
	<div class="row">
		<div class="col-md-12">
			<div class="portlet light">
				<div class="portlet-title">
					<div class="caption font-green">
						<span class="caption-subject bold uppercase">{!! Form::label('not_found', 'Data Not Found') !!}</span>
					</div>
				</div>
			</div>
		</div>
	</div>
@endif
@endsection