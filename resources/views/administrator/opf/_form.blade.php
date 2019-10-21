<div class="form-group {!! $errors->has('user_id') ? 'has-error' : '' !!}">
    {!! Form::label('user_id', 'User', ['class'=>'col-md-2 control-label']) !!}
    <div class="col-md-10">
        @if($data != '')
            {!! Form::text('user_id', $data->user->fullname, ['class'=>'form-control', 'disabled'=>'disabled']) !!}
        @else
            {!! Form::select('user_id',[''=>'']+App\Model\User::list_for_opf(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih User', 'disabled'=>isset($data->provinsi_id) ? 'disabled' : null]) !!}
        @endif
        {!! $errors->first('user_id', '<p class="help-block">:message</p>') !!}
    </div>
</div>
<div class="form-group {!! $errors->has('foto') ? 'has-error' : '' !!}">
    {!! Form::label('foto', 'Foto', ['class'=>'col-md-2 control-label']) !!}
    <div class="col-md-10">
        {!! Form::file('foto', ['class'=>'form-control']) !!}
        {!! $errors->first('foto', '<p class="help-block">:message</p>') !!}
    </div>
</div>
<div class="form-group {!! $errors->has('referal') ? 'has-error' : '' !!}">

    {!! Form::label('referal', 'Generate Referal', ['class'=>'col-md-2 control-label']) !!}

    <div class="col-md-10">
        {!! Form::text('referal', null, ['class'=>'form-control', 'id'=>'text-referal']) !!}

        {!! Form::button('Random', ['class'=>'btn btn-primary', 'id'=>'button-random']) !!}

        {!! $errors->first('referal', '<p class="help-block">:message</p>') !!}

    </div>
</div>
<br>
<div class="form-group {!! $errors->has('handphone') ? 'has-error' : '' !!}">
    {!! Form::label('handphone', 'Handphone', ['class'=>'col-md-2 control-label']) !!}
    <div class="col-md-10">
        {!! Form::text('handphone', null, ['class'=>'form-control']) !!}
        {!! $errors->first('handphone', '<p class="help-block">:message</p>') !!}
    </div>
</div>
<div class="form-group {!! $errors->has('referal_opf') ? 'has-error' : '' !!}">
    {!! Form::label('referal_opf', 'Direferalin', ['class'=>'col-md-2 control-label']) !!}
    <div class="col-md-10">
        {!! Form::text('referal_opf', null, ['class'=>'form-control']) !!}
        {!! $errors->first('referal_opf', '<p class="help-block">:message</p>') !!}
    </div>
</div>

@section('scripts')
    <script>
        $ (document).ready (function () {
            $ ("#button-random").click (function () {
                {{ $link = url('/omerchant/random/referal/') }}
                console.log ("{{ $link }}");
                $.ajax ({
                    url: "{{ $link }}",
                    type: 'GET',
                    success: function (respon) {
                        console.log (respon);
                        $ ("#text-referal").val (respon);
                    },
                });
            });
        });
    </script>
@endsection