<div class="form-group {!! $errors->has('asset_id') ? 'has-error' : '' !!}">
	{!! Form::label('asset_id', 'Asset', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		@if($data != '')
			{!! Form::text('asset_id', $data->asset->nama, ['class'=>'form-control', 'disabled'=>'disabled']) !!}
		@else
			{!! Form::select('asset_id',[''=>'']+App\Model\Asset::pluck('nama','id')->all(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih Asset']) !!}
		@endif
		{!! $errors->first('asset_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('nilai_akhir') ? 'has-error' : '' !!}">
	{!! Form::label('nilai_akhir', 'Harga Sekarang', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('nilai_akhir', null, ['class'=>'form-control']) !!}
		{!! $errors->first('nilai_akhir', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('tahun_penyusutan') ? 'has-error' : '' !!}">
	{!! Form::label('tahun_penyusutan', 'Tahun Penyusutan', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('tahun_penyusutan', null, ['class'=>'form-control']) !!}
		{!! $errors->first('tahun_penyusutan', '<p class="help-block">:message</p>') !!}
	</div>
</div>