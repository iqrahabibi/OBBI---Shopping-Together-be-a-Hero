@extends('layouts.app')

@section('content')
    <div class="page-head">
        <div class="page-title">
            <h1>Menu OPF</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption font-green">
                        <span class="caption-subject bold uppercase">Ubah OPF</span>
                    </div>
                </div>

                <div class="portlet-body">
                    {!! Form::model($data, ['route' => ['opf.update', $data],'method' =>'patch', 'class'=>'form-horizontal margin-bottom-40', 'role'=>'form','files' => true])!!}
                    <div class="form-body">
                        @include('administrator.opf._form', ['model' => $data])
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-2 col-md-10">
                                {!! Form::submit(isset($data) ? 'Update' : 'Save', ['class'=>'btn green-haze']) !!}
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

