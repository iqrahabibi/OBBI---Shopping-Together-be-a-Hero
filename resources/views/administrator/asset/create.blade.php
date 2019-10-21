@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption font-green">
                        <span class="caption-subject bold uppercase">FORM TAMBAH ASSET</span>
                    </div>
                </div>
                <div class="portlet-body form">
                    {!! Form::open(['route' => 'asset.store', 'class'=>'form-horizontal margin-bottom-40', 'role'=>'form'])!!}
                    <div class="form-body">
                        @include('administrator.asset._form')
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class='col-lg-3 pull-right'>
                                    {!! Form::reset('Reset', ['class'=>'btn btn-warning col-lg-12']) !!}
                                </div>
                                <div class='col-lg-3 pull-right'>
                                    {!! Form::submit(isset($data) ? 'Update' : 'Save', ['class'=>'btn green-haze col-lg-12']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection