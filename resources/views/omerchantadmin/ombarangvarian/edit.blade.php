@extends('layouts.app')

@section('content')
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Ubah O-Merchant Barang Varian</h3>
	</div>

	<div class="panel-body">
		{!! Form::model($om_barang_varian, ['route' => ['omerchantbarangvarian.update', $om_barang_varian],'method' =>'patch'])!!}
			@include('omerchantadmin.ombarangvarian._form', ['model' => $om_barang_varian])
			{!! Form::submit(isset($om_barang_varian) ? 'Update' : 'Save', ['class'=>'btn btn-primary']) !!}
		{!! Form::close() !!}
	</div>
</div>
@endsection

@section('content')
<div class="page-head">
	<div class="page-title">
		<h1>Menu O-Merchant Barang Varian</h1>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="portlet light">
			<div class="portlet-title">
				<div class="caption font-green">
					<span class="caption-subject bold uppercase">Ubah O-Merchant Barang Varian</span>
				</div>
			</div>
			<div class="portlet-body">
				{!! Form::model($data, ['route' => ['omerchantbarangvarian.update', $data],'method' =>'patch', 'class'=>'form-horizontal margin-bottom-40', 'role'=>'form'])!!}
					<div class="form-body">
						@include('omerchantadmin.ombarangvarian._form', ['model' => $data])
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