<div class='col-lg-12'>
    <div class="form-group {!! $errors->has('nama') ? 'has-error' : '' !!}">
        {!! Form::label('nama', 'Nama Barang', ['class'=>'col-md-2 control-label']) !!}
        <div class="col-md-10">
            {!! Form::text('nama', null, ['class'=>'form-control']) !!}
            {!! $errors->first('nama', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>
<div class='col-lg-12'>
    <div class="form-group {!! $errors->has('nomor') ? 'has-error' : '' !!}">
        {!! Form::label('nomor', 'Nomor Barang', ['class'=>'col-md-2 control-label']) !!}
        <div class="col-md-10">
            {!! Form::text('nomor', null, ['class'=>'form-control']) !!}
            {!! $errors->first('nomor', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>
<div class='col-lg-12'>
    <div class="form-group {!! $errors->has('tahun') ? 'has-error' : '' !!}">
        {!! Form::label('tahun', 'Tahun Beli', ['class'=>'col-md-2 control-label']) !!}
        <div class="col-md-2">
            {!! Form::text('tahun', null, ['class'=>'form-control', 'maxlength'=>'4']) !!}
            {!! $errors->first('tahun', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>
<div class='col-lg-12'>
    <div class="form-group {!! $errors->has('nilai') ? 'has-error' : '' !!}">
        {!! Form::label('nilai', 'Harga Beli', ['class'=>'col-md-2 control-label']) !!}
        <div class="col-md-5">
            {!! Form::text('nilai', null, ['class'=>'form-control']) !!}
            {!! $errors->first('nilai', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>