<div class="form-group {!! $errors->has('category_id') ? 'has-error' : '' !!}">
	{!! Form::label('category_id', 'Kategori', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		@if(!empty($data))
			{!! Form::text('nama_kategori', $data->category->nama_kategori, ['class'=>'form-control', 'readonly']) !!}
			{!! Form::hidden('category_id', $data->category_id, ['class'=>'form-control']) !!}
		@else
			{!! Form::select('category_id',[''=>'']+App\Model\Category::list(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih Kategori', 'disabled'=>isset($data->category_id) ? 'disabled' : null]) !!}
		@endif
		{!! $errors->first('category_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('nama_barang') ? 'has-error' : '' !!}">
	{!! Form::label('nama_barang', 'Nama Barang', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('nama_barang', null, ['class'=>'form-control']) !!}
		{!! $errors->first('nama_barang', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('sku') ? 'has-error' : '' !!}">
	{!! Form::label('sku', 'SKU', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('sku', null, ['class'=>'form-control']) !!}
		{!! $errors->first('sku', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('brand') ? 'has-error' : '' !!}">
	{!! Form::label('brand', 'Brand', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('brand', null, ['class'=>'form-control']) !!}
		{!! $errors->first('brand', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('weight') ? 'has-error' : '' !!}">
	{!! Form::label('weight', 'Berat (gram)', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('weight', null, ['class'=>'form-control']) !!}
		{!! $errors->first('weight', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('deskripsi') ? 'has-error' : '' !!}">
	{!! Form::label('deskripsi', 'Deskripsi', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::textarea('deskripsi', null, ['class'=>'form-control']) !!}
		{!! $errors->first('deskripsi', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('jumlah_amal') ? 'has-error' : '' !!}">
	{!! Form::label('jumlah_amal', 'Amal', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('jumlah_amal', null, ['class'=>'form-control']) !!}
		{!! $errors->first('jumlah_amal', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('keuntungan') ? 'has-error' : '' !!}">
	{!! Form::label('keuntungan', 'Keuntungan', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('keuntungan', null, ['class'=>'form-control']) !!}
		{!! $errors->first('keuntungan', '<p class="help-block">:message</p>') !!}
	</div>
</div>