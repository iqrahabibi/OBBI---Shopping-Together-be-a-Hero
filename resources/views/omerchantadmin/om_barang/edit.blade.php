@extends('layouts.app')

@section('content')
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Ubah O-Merchant Barang</h3>
	</div>

	<div class="panel-body">
		{!! Form::model($om_barang, ['route' => ['om_barang.update', $om_barang],'method' =>'patch', 'files'=>'true'])!!}
			@include('transaksi.om_barang._form', ['model' => $om_barang])
			{!! Form::submit(isset($om_barang) ? 'Update' : 'Save', ['class'=>'btn btn-primary']) !!}
		{!! Form::close() !!}
	</div>
</div>
@endsection