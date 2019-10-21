@if(empty($data->parent_id))
	<div class="form-group {!! $errors->has('parent_id') ? 'has-error' : '' !!}">
		{!! Form::label('parent_id', 'Parent Barang Conversi', ['class'=>'col-md-2 control-label']) !!}
		<div class="col-md-10">
			{!! Form::select('parent_id',[''=>'']+App\Model\BarangConversi::pluck('satuan', 'id')->all(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih Parent']) !!}
			{!! $errors->first('parent_id', '<p class="help-block">:message</p>') !!}
		</div>
	</div>
	<div class="form-group {!! $errors->has('jumlah') ? 'has-error' : '' !!}">
		{!! Form::label('jumlah', 'Jumlah untuk Parent', ['class'=>'col-md-2 control-label']) !!}
		<div class="col-md-10">
			{!! Form::text('jumlah', 0, ['class'=>'form-control']) !!}
			{!! $errors->first('jumlah', '<p class="help-block">:message</p>') !!}
		</div>
	</div>
@else
	<div class="form-group {!! $errors->has('parent_id') ? 'has-error' : '' !!}">
		{!! Form::label('parent_id', 'Parent Barang Conversi', ['class'=>'col-md-2 control-label']) !!}
		<div class="col-md-10">
			{!! Form::select('parent_id',[''=>'']+App\Model\BarangConversi::pluck('satuan', 'id')->all(), isset($parent) ? $parent->id : -1,['class'=>'js-selectize', 'placeholder'=>'Pilih Parent']) !!}
			{!! $errors->first('parent_id', '<p class="help-block">:message</p>') !!}
		</div>
	</div>
	<div class="form-group {!! $errors->has('jumlah') ? 'has-error' : '' !!}">
		{!! Form::label('jumlah', 'Jumlah untuk Parent', ['class'=>'col-md-2 control-label']) !!}
		<div class="col-md-10">
			{!! Form::text('jumlah', isset($parent) ? $parent->jumlah : 0, ['class'=>'form-control']) !!}
			{!! $errors->first('jumlah', '<p class="help-block">:message</p>') !!}
		</div>
	</div>
@endif
<div class="form-group {!! $errors->has('satuan') ? 'has-error' : '' !!}">
	{!! Form::label('satuan', 'Satuan', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('satuan', null, ['class'=>'form-control']) !!}
		{!! $errors->first('satuan', '<p class="help-block">:message</p>') !!}
	</div>
</div>