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
<div class="form-group {!! $errors->has('gambar_barang') ? 'has-error' : '' !!}">
    {!! Form::label('gambar_barang', 'Gambar Barang', ['class'=>'col-md-2 control-label']) !!}
    <div class="col-md-10">
        {!! Form::file('gambar_barang', null, ['class'=>'form-control']) !!}
        {!! $errors->first('gambar_barang', '<p class="help-block">:message</p>') !!}
        @if(!empty($data))
            <img src="{{$data->gambar_barang}}" alt="" width="400" height="300">
        @endif
    </div>
</div>