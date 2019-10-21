@extends('layouts.app')

@section('content')
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Tambah OMerchant Barang Promosi</h3>
	</div>

	<div class="panel-body">
		{!! Form::open(['route' => 'omerchantbarangpromosi.store'])!!}
			@include('omerchantadmin.omerchantbarangpromosi._form')
			{!! Form::submit(isset($data) ? 'Update' : 'Save', ['class'=>'btn btn-primary']) !!}
		{!! Form::close() !!}
	</div>
</div>
@endsection