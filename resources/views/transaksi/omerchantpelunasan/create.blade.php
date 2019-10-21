@extends('layouts.app')

@section('content')
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Tambah OMerchant Pelunasan</h3>
	</div>

	<div class="panel-body">
		{!! Form::open(['route' => 'ompelunasan.store'])!!}
			@include('transaksi.omerchantpelunasan._form')
			{!! Form::submit('Save', ['class'=>'btn btn-primary']) !!}
		{!! Form::close() !!}
	</div>
</div>
@endsection