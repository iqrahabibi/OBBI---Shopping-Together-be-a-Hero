<div class="form-group {!! $errors->has('barang_id') ? 'has-error' : '' !!}">
    {!! Form::label('barang_id', 'Barang', ['class'=>'col-md-2 control-label']) !!}
    <div class="col-md-10">
        @if(empty($data))
            {!! Form::select('barang_id',[''=>'']+App\Model\Barang::pluck('nama_barang','id')->all(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih Barang','disabled'=>isset($om_barang_varian->barang_id) ? 'disabled' : null]) !!}
        @else
            {!! Form::text('nama_barang', $data->barang->nama_barang,['class'=>'form-control','readonly']) !!}
            {!! Form::hidden('barang_id',$data->barang_id,['class' => 'form-control']) !!}
        @endif
        {!! $errors->first('barang_id', '<p class="help-block">:message</p>') !!}
    </div>
</div>
<div class="form-group {!! $errors->has('qty') ? 'has-error' : '' !!}">
    {!! Form::label('qty', 'Quantity', ['class'=>'col-md-2 control-label']) !!}
    <div class="col-md-10">
        {!! Form::number('qty', null, ['class'=>'form-control']) !!}
        {!! $errors->first('qty', '<p class="help-block">:message</p>') !!}
    </div>
</div>
<div class="form-group {!! $errors->has('onhold_qty') ? 'has-error' : '' !!}">
    {!! Form::label('onhold_qty', 'Onhold Quantity', ['class'=>'col-md-2 control-label']) !!}
    <div class="col-md-10">
        {!! Form::number('onhold_qty', null, ['class'=>'form-control']) !!}
        {!! $errors->first('onhold_qty', '<p class="help-block">:message</p>') !!}
    </div>
</div>
<div class="form-group {!! $errors->has('minimal_qty') ? 'has-error' : '' !!}">
    {!! Form::label('minimal_qty', 'Minimal Quantity', ['class'=>'col-md-2 control-label']) !!}
    <div class="col-md-10">
        {!! Form::number('minimal_qty', null, ['class'=>'form-control']) !!}
        {!! $errors->first('minimal_qty', '<p class="help-block">:message</p>') !!}
    </div>
</div>
<div class="form-group {!! $errors->has('urut') ? 'has-error' : '' !!}">
    {!! Form::label('urut', 'Urutan', ['class'=>'col-md-2 control-label']) !!}
    <div class="col-md-10">
        {!! Form::number('urut', null, ['class'=>'form-control']) !!}
        {!! $errors->first('urut', '<p class="help-block">:message</p>') !!}
    </div>
</div>
<div class="form-group {!! $errors->has('Harga') ? 'has-error' : '' !!}">
    {!! Form::label('harga', 'Harga', ['class'=>'col-md-2 control-label']) !!}
    <div class="col-md-10">
        {!! Form::number('harga', null, ['class'=>'form-control']) !!}
        {!! $errors->first('harga', '<p class="help-block">:message</p>') !!}
    </div>
</div>