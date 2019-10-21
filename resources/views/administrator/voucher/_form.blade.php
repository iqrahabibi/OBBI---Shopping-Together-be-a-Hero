<div class="form-group {!! $errors->has('jml_voucher') ? 'has-error' : '' !!}">
	{!! Form::label('jml_voucher', 'Jumlah Voucher', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('jml_voucher', null, ['class'=>'form-control']) !!}
		{!! $errors->first('jml_voucher', '<p class="help-block">:message</p>') !!}
	</div>
</div>
