@extends('layouts.app')

@section('styles')
<link href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="page-head">
	<div class="page-title">
		<h1>List Menu</h1>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="portlet light">
			<div class="portlet-title">
				<div class="caption font-green">
					<span class="caption-subject bold uppercase">
                        <a href="{{ route('menu.create') }}" class="btn green-haze">Menu Baru</a>
                    </span>
				</div>
			</div>
			<div class="portlet-body">
				{!! $html->table(['class'=>'table table-striped table-bordered dt-responsive nowrap', 'cellspacing'=>'0',  'width'=>'100%']) !!}
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
{!! $html->scripts() !!}
@endsection