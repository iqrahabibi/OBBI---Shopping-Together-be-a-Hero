<div class="form-group {!! $errors->has('kode_usaha') ? 'has-error' : '' !!}">
    {!! Form::label('kode_usaha', 'List Usaha OMerchant', ['class'=>'col-md-2 control-label']) !!}
    <div class="col-md-10">
        @if(empty($data))
            {!! Form::select('kode_usaha',[''=>'']+App\Model\UsahaOMerchant::list_for_omerchant_admin(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih Usaha','disabled'=>isset($data->kode_usaha) ? 'disabled' : null]) !!}
        @else
            {!! Form::text('nama_usaha', $data->usaha->usaha->nama_usaha,['class'=>'form-control','readonly']) !!}
            {!! Form::hidden('kode_usaha',$data->kode_usaha,['class' => 'form-control']) !!}
        @endif
        {!! $errors->first('kode_usaha', '<p class="help-block">:message</p>') !!}
    </div>
</div>
<div class="form-group {!! $errors->has('nama_kategori') ? 'has-error' : '' !!}">
    {!! Form::label('nama_kategori', 'Nama Kategori', ['class'=>'col-md-2 control-label']) !!}
    <div class="col-md-10">
        {!! Form::text('nama_kategori', null, ['class'=>'form-control']) !!}
        {!! $errors->first('nama_kategori', '<p class="help-block">:message</p>') !!}
    </div>
</div>
<div class="form-group {!! $errors->has('deskripsi') ? 'has-error' : '' !!}">
    {!! Form::label('deskripsi', 'Deskripsi', ['class'=>'col-md-2 control-label']) !!}
    <div class="col-md-10">
        {!! Form::textarea('deskripsi', null, ['class'=>'form-control']) !!}
        {!! $errors->first('deskripsi', '<p class="help-block">:message</p>') !!}
    </div>
</div>