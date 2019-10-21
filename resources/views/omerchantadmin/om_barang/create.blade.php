@extends('layouts.app')

@section('content')
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Tambah O-Merchant Barang</h3>
	</div>

	<div class="panel-body">
		{!! Form::open(['route' => 'om_barang.store'])!!}
			@include('transaksi.om_barang._form')
			{!! Form::submit(isset($data) ? 'Update' : 'Save', ['class'=>'btn btn-primary']) !!}
		{!! Form::close() !!}
	</div>
</div>
@endsection