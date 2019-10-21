<div class="form-group {!! $errors->has('barang_id') ? 'has-error' : '' !!}">
    {!! Form::label('barang_id', 'Barang', ['class'=>'col-md-2 control-label']) !!}
    <div class="col-md-10">
        @if(!empty($data))
            {!! Form::text('nama_barang', $data->barang->nama_barang, ['class'=>'form-control', 'readonly']) !!}
            {!! Form::hidden('barang_id', $data->barang_id, ['class'=>'form-control']) !!}
        @else
            {!! Form::select('barang_id',[''=>'']+App\Model\Barang::list(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih Barang', 'disabled'=>isset($data->barang_id) ? 'disabled' : null]) !!}
        @endif
        {!! $errors->first('barang_id', '<p class="help-block">:message</p>') !!}
    </div>
</div>
<div class="form-group {!! $errors->has('gambar_barang') ? 'has-error' : '' !!}">
    {!! Form::label('gambar_barang', 'Gambar Barang', ['class'=>'col-md-2 control-label']) !!}
    <div class="col-md-10">
        {!! Form::file('gambar_barang', ['class'=>'form-control']) !!}
        {!! $errors->first('gambar_barang', '<p class="help-block">:message</p>') !!}

        @if(!empty($data))
            <img src="{{ \App\Helper\ObbiAssets::get_asset(\App\Helper\ObbiAssets::BARANG,$data->gambar_barang) }}" width="400" class="img-responsive">
        @endif
    </div>
</div>