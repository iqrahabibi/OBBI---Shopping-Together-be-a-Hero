@extends('layouts.app')

@section('content')
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Ubah Status</h3>
	</div>

	<div class="panel-body">
		{!! Form::model($data, ['route' => ['pengaduan.updateaduan', $data]])!!}
			@if($data->valid == 0)
			<div class="form-group {!! $errors->has('valid') ? 'has-error' : '' !!}">
				{!! Form::label('valid', 'Valid') !!}
				<p class="form-control">{!! Form::radio('valid', 1, isset($data) && $data->valid ? true : false) !!} Yes</p>
				<p class="form-control">{!! Form::radio('valid', 0, isset($data) && $data->valid ? true : false) !!} No</p>
				{!! $errors->first('valid', '<p class="help-block">:message</p>') !!}
			</div>
            {!! Form::submit(isset($data) ? 'Update' : 'Save', ['class'=>'btn btn-primary']) !!}
            @else
            <div class="form-group {!! $errors->has('valid') ? 'has-error' : '' !!}">
                {!! Form::label('valid', 'Pengaduan sudah pernah di Update status') !!}
            </div>
			@endif
		{!! Form::close() !!}
	</div>
</div>
@endsection
