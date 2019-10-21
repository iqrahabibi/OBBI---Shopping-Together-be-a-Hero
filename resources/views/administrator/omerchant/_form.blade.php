<div class="form-group {!! $errors->has('user_id') ? 'has-error' : '' !!}">
	{!! Form::label('user_id', 'User', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		@if(empty($data))
			{!! Form::select('user_id',[''=>'']+App\Model\User::list_for_omerchant(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih User']) !!}
		@else
			{!! Form::text('user_id', $data->user->fullname, ['class'=>'form-control', 'readonly']) !!}
		@endif
		{!! $errors->first('user_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('referal') ? 'has-error' : '' !!}">
		{!! Form::label('referal', 'Generate Referal', ['class'=>'col-md-2 control-label']) !!}
		<div class="col-md-10">
			{!! Form::text('referal', null, ['class'=>'form-control', 'id'=>'text-referal']) !!}
			{!! Form::button('Random', ['class'=>'btn btn-primary', 'id'=>'button-random']) !!}
			{!! $errors->first('referal', '<p class="help-block">:message</p>') !!}
		</div>
</div>
<br>
<div class="form-group {!! $errors->has('referal_omerchant') ? 'has-error' : '' !!}">
	{!! Form::label('referal_omerchant', 'Direferalin', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('referal_omerchant', null, ['class'=>'form-control']) !!}
		{!! $errors->first('referal_omerchant', '<p class="help-block">:message</p>') !!}
	</div>
</div>

@section('scripts')
<script>
$(document).ready(function () {
	$("#button-random").click(function () {
		{{ $link = url('/omerchant/random/referal/') }}
		console.log("{{ $link }}");
		$.ajax({
			url: "{{ $link }}",
			type: 'GET',
			success: function(respon) {
				console.log(respon);
				$("#text-referal").val(respon);
			},
		});
	});
});
</script>
@endsection