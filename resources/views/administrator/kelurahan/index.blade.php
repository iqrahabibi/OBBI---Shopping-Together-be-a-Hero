@extends('layouts.app')

@section('styles')
<link href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="page-head">
	<div class="page-title">
		<h1>Menu Kelurahan</h1>
	</div>
</div>
<ul class="page-breadcrumb breadcrumb">
    <li>
        <p>Showing 10.000 datas per page</p>
    </li>
</ul>
<div class="row">
	<div class="col-md-12">
		<div class="portlet light">
			<div class="portlet-title">
				<div class="caption font-green">
					<span class="caption-subject bold uppercase">
                        @if(app('request')->input('page') > 1)
                            <a href="{{ route('kelurahan.index') }}/?page={{ app('request')->input('page')-1 }}" class="btn green-haze">Before</a>
                            <a href="{{ route('kelurahan.index') }}/?page={{ app('request')->input('page')+1 }}" class="btn green-haze">Next</a>
                        @else
                            <a href="{{ route('kelurahan.index') }}/?page=2" class="btn green-haze">Next</a>
                        @endif

                        <a href="{{ route('kelurahan.create') }}" class="btn green-haze">Kelurahan Baru</a>
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