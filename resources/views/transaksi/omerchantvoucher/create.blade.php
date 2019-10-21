@extends('layouts.app')

@section('content')
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Tambah OMerchant Voucher</h3>
	</div>

	<div class="panel-body">
		{!! Form::open(['route' => 'omvoucher.store'])!!}
			@include('transaksi.omerchantvoucher._form')
			{!! Form::submit('Save', ['class'=>'btn btn-primary']) !!}
		{!! Form::close() !!}
	</div>
</div>
@endsection