@extends('layouts.app')

@section('content')
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Ubah OMerchant Pelunasan</h3>
	</div>

	<div class="panel-body">
		{!! Form::model($data, ['route' => ['ompelunasan.update', $data],'method' =>'patch'])!!}
			@include('transaksi.omerchantpelunasan._form', ['model' => $data])
			{!! Form::submit(isset($data) ? 'Update' : 'Save', ['class'=>'btn btn-primary']) !!}
		{!! Form::close() !!}
	</div>
</div>
@endsection
