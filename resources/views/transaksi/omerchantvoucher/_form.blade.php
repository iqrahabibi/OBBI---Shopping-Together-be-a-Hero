<div class="form-group {!! $errors->has('jml_om_voucher') ? 'has-error' : '' !!}">
	{!! Form::label('jml_om_voucher', 'Jumlah OMerchant Voucher') !!}
	{!! Form::text('jml_om_voucher', null, ['class'=>'form-control']) !!}
	{!! $errors->first('jml_om_voucher', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {!! $errors->has('tipe') ? 'has-error' : '' !!}">
	{!! Form::label('tipe', 'Tipe') !!}
	{!! Form::select('tipe',['GYRO'=>'GYRO','CEK'=>'CEK','TRANSFER'=>'TRANSFER'],null,['class'=>'js-selectize', 'placeholder'=>'Pilih Tipe'])!!}
	{!! $errors->first('tipe', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {!! $errors->has('tanggal') ? 'has-error' : '' !!}">
	{!! Form::label('tanggal', 'Tanggal') !!}
	{!! Form::date('tanggal', null, ['class'=>'form-control']) !!}
	{!! $errors->first('tanggal', '<p class="help-block">:message</p>') !!}
</div>
