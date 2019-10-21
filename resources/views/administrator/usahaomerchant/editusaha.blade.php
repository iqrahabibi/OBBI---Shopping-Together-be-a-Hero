@extends('layouts.app')

@section('content')
<div class="page-head">
	<div class="page-title">
		<h1>Menu OMerchant</h1>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="portlet light">
			<div class="portlet-title">
				<div class="caption font-green">
					<span class="caption-subject bold uppercase">Ubah OMerchant</span>
				</div>
			</div>
			<div class="portlet-body">
				{!! Form::model($data, ['route' => ['omerchant.update', $data],'method' =>'patch', 'class'=>'form-horizontal margin-bottom-40', 'role'=>'form'])!!}
					<div class="form-body">
						@include('administrator.omerchant._form', ['model' => $data])
						<div class="form-group {!! $errors->has('level') ? 'has-error' : '' !!}">
								{!! Form::label('level', 'Penanggung Jawab', ['class'=>'col-md-2 control-label']) !!}
								<div class="col-md-10">
									<p class="form-control">{!! Form::radio('level', 1, isset($data) && $data->level ? true : false, ['id'=>'button-level-ya']) !!} 
										{!! Form::label('label_level_ya', 'Ya', ['id'=>'label-level-ya']) !!}
									</p>
									<p class="form-control">{!! Form::radio('level', 0, isset($data) && $data->level ? true : false, ['id'=>'button-level-tidak']) !!} 
										{!! Form::label('label_level_tidak', 'Tidak', ['id'=>'label-level-tidak']) !!}
									</p>
									{!! $errors->first('level', '<p class="help-block">:message</p>') !!}
								</div>
							</div>
							
							<div class="form-group {!! $errors->has('porsi') ? 'has-error' : '' !!}" id="form-porsi">
								{!! Form::label('porsi', 'Porsi Bagi Hasil', ['class'=>'col-md-2 control-label']) !!}
								<div class="col-md-10">
									{!! Form::text('porsi', null, ['class'=>'form-control']) !!}
									{!! $errors->first('porsi', '<p class="help-block">:message</p>') !!}
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


@section('scripts')
<script>
$(document).ready(function () {
	$("#label-level-ya").click(function () {
        $("#button-level-ya").filter('[value=1]').prop('checked', true);
	});
	$("#label-level-tidak").click(function () {
        $("#button-level-tidak").filter('[value=0]').prop('checked', true);
	});
});
</script>
@endsection
