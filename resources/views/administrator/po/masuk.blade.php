@extends('layouts.app')

@section('content')
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Purchasing Order Masuk</h3>
	</div>

	<div class="panel-body">
		{!! Form::model($data, ['route' => ['po.masuksave', $data],'method' =>'post'])!!}
			@include('administrator.po._formpomasuk', ['model' => $data])
			{!! Form::submit('Save', ['class'=>'btn btn-primary']) !!}
		{!! Form::close() !!}
	</div>
</div>
@endsection
