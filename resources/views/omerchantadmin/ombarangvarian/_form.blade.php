<div class="form-group {!! $errors->has('barang_id') ? 'has-error' : '' !!}">
    {!! Form::label('barang_id', 'Barang', ['class'=>'col-md-2 control-label']) !!}
    <div class="col-md-10">
        @if(empty($om_barang_varian))
            {!! Form::select('barang_id',[''=>'']+App\Model\Barang::pluck('nama_barang','id')->all(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih Barang','disabled'=>isset($om_barang_varian->barang_id) ? 'disabled' : null]) !!}
        @else
            {!! Form::text('nama_barang', $om_barang_varian->barang->nama_barang,['class'=>'form-control','readonly']) !!}
            {!! Form::hidden('barang_id',$om_barang_varian->barang_id,['class' => 'form-control']) !!}
        @endif
        {!! $errors->first('barang_id', '<p class="help-block">:message</p>') !!}
    </div>
</div>
<div class="form-group {!! $errors->has('varian_barang') ? 'has-error' : '' !!}">
    {!! Form::label('varian_barang', 'Varian Barang', ['class'=>'col-md-2 control-label']) !!}
    <div class="col-md-10">
        {!! Form::text('varian_barang', null, ['class'=>'form-control']) !!}
        {!! $errors->first('varian_barang', '<p class="help-block">:message</p>') !!}
    </div>
</div>