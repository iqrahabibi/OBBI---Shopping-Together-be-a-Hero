@extends('layouts.app')

@section('content')
<div class="page-head">
	<div class="page-title">
		<h1>Menu Data User</h1>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="portlet light">
			<div class="portlet-title">
				<div class="caption font-green">
					<span class="caption-subject bold uppercase">Ubah Data User</span>
				</div>
			</div>
			<div class="portlet-body">
				{!! Form::model($data, ['route' => ['member.update', $data],'method' =>'patch', 'class'=>'form-horizontal margin-bottom-40', 'role'=>'form'])!!}
					<div class="form-body">
						<div class="form-group {!! $errors->has('fullname') ? 'has-error' : '' !!}">
							{!! Form::label('fullname', 'Fullname', ['class'=>'col-md-2 control-label']) !!}
							<div class="col-md-10">
								{!! Form::text('fullname', null, ['class'=>'form-control']) !!}
								{!! $errors->first('fullname', '<p class="help-block">:message</p>') !!}
							</div>
						</div>
						<div class="form-group {!! $errors->has('email') ? 'has-error' : '' !!}">
							{!! Form::label('email', 'Email', ['class'=>'col-md-2 control-label']) !!}
							<div class="col-md-10">
								{!! Form::text('email', null, ['class'=>'form-control', 'readonly']) !!}
								{!! $errors->first('email', '<p class="help-block">:message</p>') !!}
							</div>
						</div>
						<div class="form-group {!! $errors->has('status') ? 'has-error' : '' !!}">
							{!! Form::label('status', 'Status', ['class'=>'col-md-2 control-label']) !!}
							<div class="col-md-10">
								<div class="md-radio-inline">
									<div class="md-radio">
										{!! Form::radio('status', '1', isset($data) && $data->status == 1 ? true : false, ['class'=>'md-radiobtn', 'id'=>'radio_yes']) !!}
										<label for="radio_yes">
										<span></span>
										<span class="check"></span>
										<span class="box"></span>
										Normal</label>
									</div>
									<div class="md-radio">
										{!! Form::radio('status', '0', isset($data) && $data->status == 0 ? true : false, ['class'=>'md-radiobtn', 'id'=>'radio_no']) !!}
										<label for="radio_no">
										<span></span>
										<span class="check"></span>
										<span class="box"></span>
										Banned</label>
									</div>
								</div>
							</div>
						</div>
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
