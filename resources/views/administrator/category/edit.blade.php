@extends('layouts.app')

@section('content')
<div class="page-head">
	<div class="page-title">
		<h1>Menu Kategori</h1>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="portlet light">
			<div class="portlet-title">
				<div class="caption font-green">
					<span class="caption-subject bold uppercase">Ubah Kategori</span>
				</div>
			</div>
			<div class="portlet-body">
				{!! Form::model($data, ['route' => ['category.update', $data],'method' =>'patch', 'class'=>'form-horizontal margin-bottom-40', 'role'=>'form'])!!}
					<div class="form-body">
						@include('administrator.category._form', ['model' => $data])
						@if($data->valid == 1)
							<div class="form-group {!! $errors->has('valid') ? 'has-error' : '' !!}">
								{!! Form::label('valid', 'Valid', ['class'=>'col-md-2 control-label']) !!}
								<div class="col-md-10">
									<div class="md-radio-inline">
										<div class="md-radio">
											{!! Form::radio('valid', 1, true, ['class'=>'md-radiobtn', 'id'=>'radio_yes']) !!}
											<label for="radio_yes">
											<span></span>
											<span class="check"></span>
											<span class="box"></span>
											Yes</label>
										</div>
										<div class="md-radio">
											{!! Form::radio('valid', 0, isset($data) && $data->valid ? true : false, ['class'=>'md-radiobtn', 'id'=>'radio_no']) !!}
											<label for="radio_no">
											<span></span>
											<span class="check"></span>
											<span class="box"></span>
											No</label>
										</div>
									</div>
								</div>
							</div>
						@endif
					</div>
					<div class="form-actions">
						<div class="row">
							<div class="col-md-offset-2 col-md-10">
								{!! Form::submit(isset($data) ? 'Update' : 'Save', ['class'=>'btn btn-primary']) !!}
							</div>
						</div>
					</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>
@endsection
