@extends('layouts.app')

@section('content')
<div class="page-head">
	<div class="page-title">
		<h1>Menu Usaha</h1>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="portlet light">
			<div class="portlet-title">
				<div class="caption font-green">
					<span class="caption-subject bold uppercase">Tambah Usaha</span>
				</div>
			</div>
			<div class="portlet-body form">
				{!! Form::open(['route' => 'usaha.store', 'class'=>'form-horizontal margin-bottom-40', 'role'=>'form'])!!}
					<div class="form-body">
						<div class="form-group {!! $errors->has('provinsi_id') ? 'has-error' : '' !!}">
							{!! Form::label('provinsi_id', 'Provinsi', ['class'=>'col-md-2 control-label']) !!}
							<div class="col-md-10">
								{!! Form::select('provinsi_id',[''=>'']+App\Model\Provinsi::pluck('nama_provinsi','id')->all(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih Provinsi']) !!}
								{!! $errors->first('provinsi_id', '<p class="help-block">:message</p>') !!}
							</div>
						</div>
						<div class="form-group {!! $errors->has('kota_id') ? 'has-error' : '' !!}">
							{!! Form::label('kota_id', 'Kota', ['class'=>'col-md-2 control-label']) !!}
							<div class="col-md-10">
								{!! Form::select('kota_id',[''=>''], null,['class'=>'js-selectize', 'placeholder'=>'Pilih Kota']) !!}
								{!! $errors->first('kota_id', '<p class="help-block">:message</p>') !!}
							</div>
						</div>
						<div class="form-group {!! $errors->has('kecamatan_id') ? 'has-error' : '' !!}">
							{!! Form::label('kecamatan_id', 'Kecamatan', ['class'=>'col-md-2 control-label']) !!}
							<div class="col-md-10">
								{!! Form::select('kecamatan_id',[''=>''], null,['class'=>'js-selectize', 'placeholder'=>'Pilih Kecamatan', 'disabled'=>isset($data->kecamatan_id) ? 'disabled' : null]) !!}
								{!! $errors->first('kecamatan_id', '<p class="help-block">:message</p>') !!}
							</div>
						</div>
					
						@include('administrator.usaha._form')
					</div>
					<div class="form-actions">
						<div class="row">
							<div class="col-md-offset-2 col-md-10">
								{!! Form::submit('Save', ['class'=>'btn green-haze']) !!}
							</div>
						</div>
					</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>
@endsection