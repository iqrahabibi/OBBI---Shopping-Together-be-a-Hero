@extends('layouts.app')

@section('content')
<div class="page-head">
	<div class="page-title">
		<h1>Menu License</h1>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="portlet light">
			<div class="portlet-title">
				<div class="caption font-green">
					<span class="caption-subject bold uppercase">Tambah License</span>
				</div>
			</div>
			<div class="portlet-body form">
				{!! Form::open(['route' => 'license.store', 'class'=>'form-horizontal margin-bottom-40', 'role'=>'form', 'files'=>'true'])!!}
					<div class="form-body">
						<div class="form-group {!! $errors->has('kriteria_license_id') ? 'has-error' : '' !!}">
							{!! Form::label('kriteria_license_id', 'Kriteria License', ['class'=>'col-md-2 control-label']) !!}
							<div class="col-md-10">
							{!! Form::select('kriteria_license_id',[''=>'']+App\Model\KriteriaLicense::pluck('nama_kriteria_license','id')->all(), null, ['class'=>'js-selectize', 'placeholder'=>'Pilih Kriteria License']) !!}
								<div class="form-control-focus"></div>
								{!! $errors->first('kriteria_license_id', '<p class="help-block">:message</p>') !!}
							</div>
						</div>
						<div class="form-group {!! $errors->has('provinsi_id') ? 'has-error' : '' !!}">
							{!! Form::label('provinsi_id', 'Provinsi', ['class'=>'col-md-2 control-label']) !!}
							<div class="col-md-10">
								{!! Form::select('provinsi_id',[''=>'']+App\Model\Provinsi::pluck('nama_provinsi','id')->all(), null,
									['class'=>'js-selectize', 'placeholder'=>'Pilih Provinsi']) !!}
								<div class="form-control-focus"></div>
								{!! $errors->first('provinsi_id', '<p class="help-block">:message</p>') !!}
							</div>
						</div>
						<div class="form-group {!! $errors->has('kota_id') ? 'has-error' : '' !!}">
							{!! Form::label('kota_id', 'Kabupaten / Kota', ['class'=>'col-md-2 control-label']) !!}
							<div class="col-md-10">
								{!! Form::select('kota_id',[''=>''], null,['class'=>'js-selectize', 'placeholder'=>'Pilih kota / Kota']) !!}
								<div class="form-control-focus"></div>
								{!! $errors->first('kota_id', '<p class="help-block">:message</p>') !!}
							</div>
						</div>
						<div class="form-group {!! $errors->has('kecamatan_id') ? 'has-error' : '' !!}">
							{!! Form::label('kecamatan_id', 'Kecamatan', ['class'=>'col-md-2 control-label']) !!}
							<div class="col-md-10">
								{!! Form::select('kecamatan_id',[''=>''], null,['class'=>'js-selectize', 'placeholder'=>'Pilih Kecamatan']) !!}
								<div class="form-control-focus"></div>
								{!! $errors->first('kecamatan_id', '<p class="help-block">:message</p>') !!}
							</div>
						</div>
						<div class="form-group {!! $errors->has('kelurahan_id') ? 'has-error' : '' !!}">
							{!! Form::label('kelurahan_id', 'Kelurahan', ['class'=>'col-md-2 control-label']) !!}
							<div class="col-md-10">
								{!! Form::select('kelurahan_id',[''=>''], null,['class'=>'js-selectize', 'placeholder'=>'Pilih Kelurahan']) !!}
								<div class="form-control-focus"></div>
								{!! $errors->first('kelurahan_id', '<p class="help-block">:message</p>') !!}
							</div>
						</div>

						@include('administrator.license._form')
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
