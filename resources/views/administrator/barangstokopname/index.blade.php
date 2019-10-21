@extends('layouts.app')

@section('styles')
<link href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="panel panel-default">
    <div class="panel-heading">
        <h2 class="panel-title">Barang Stok Opname</h2>
    </div>
    <div class="panel-body">
            <small><a href="{{ route('barangstokopname.create') }}" class="btn btn-primary btn-sm">Barang Stok Opname Baru</a></small>
    </div>
    <div class="panel-body">
        {!! $html->table(['class'=>'table table-striped table-bordered dt-responsive nowrap', 'cellspacing'=>'0',  'width'=>'100%']) !!}
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
{!! $html->scripts() !!}
@endsection