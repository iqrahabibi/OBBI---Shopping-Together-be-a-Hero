<div class="form-group {!! $errors->has('nama_tipe_donasi') ? 'has-error' : '' !!}">
	{!! Form::label('nama_tipe_donasi', 'Nama Tipe Donasi', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('nama_tipe_donasi', null, ['class'=>'form-control']) !!}
		{!! $errors->first('nama_tipe_donasi', '<p class="help-block">:message</p>') !!}
	</div>
</div>