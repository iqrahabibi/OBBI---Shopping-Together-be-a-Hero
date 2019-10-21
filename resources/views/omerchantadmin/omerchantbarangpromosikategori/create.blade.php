@extends('layouts.app')

@section('content')
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Tambah OMerchant Barang Promosi kategori</h3>
	</div>

	<div class="panel-body">
		{!! Form::open(['route' => 'omerchantbarangpromosikategori.store'])!!}
			@include('omerchantadmin.omerchantbarangpromosikategori._form')
			{!! Form::submit(isset($data) ? 'Update' : 'Save', ['class'=>'btn btn-primary']) !!}
		{!! Form::close() !!}
	</div>
</div>
@endsection

@section('content')
<div class="page-head">
	<div class="page-title">
		<h1>Menu OMerchant Barang Promosi kategori</h1>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="portlet light">
			<div class="portlet-title">
				<div class="caption font-green">
					<span class="caption-subject bold uppercase">Tambah OMerchant Barang Promosi kategori</span>
				</div>
			</div>
			<div class="portlet-body form">
				{!! Form::open(['route' => 'omerchantbarangpromosikategori.store', 'class'=>'form-horizontal margin-bottom-40', 'role'=>'form'])!!}
					<div class="form-body">
						@include('omerchantadmin.omerchantbarangpromosikategori._form')
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