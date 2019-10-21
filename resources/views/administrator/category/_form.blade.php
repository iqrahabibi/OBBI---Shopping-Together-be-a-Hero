<div class="form-group {!! $errors->has('group_id') ? 'has-error' : '' !!}">
	{!! Form::label('group_id', 'Group', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		@if($data != '')
			{!! Form::text('group_id', $data->group->nama_group, ['class'=>'form-control', 'disabled'=>'disabled']) !!}
		@else
			{!! Form::select('group_id',[''=>'']+App\Model\Group::pluck('nama_group','id')->all(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih Group', 'disabled'=>isset($data->provinsi_id) ? 'disabled' : null]) !!}
		@endif
		{!! $errors->first('group_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('nama_kategori') ? 'has-error' : '' !!}">
	{!! Form::label('nama_kategori', 'Nama Kategori', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('nama_kategori', null, ['class'=>'form-control']) !!}
		{!! $errors->first('nama_kategori', '<p class="help-block">:message</p>') !!}
	</div>
</div>