@extends('layouts.admin_layots')

@section('content')
<section class="content-header">
    <h1>
        Laporan Transaksi Saldo Amal
        <small>Control panel</small>
    </h1>
	<br>
	
	
	<h1><button class='btn btn-danger' role="button" title='Print PDF' id='view-document' data-toggle="collapse" href="#collapse-pdf" aria-expanded="false" aria-controls="collapse-pdf"> Export To PDF &nbsp;
	<i class='fa fa-file-pdf-o'></i></button></h1>
	
	<br>
	{{-- Collapse PDF --}}
  <div class="collapse" id="collapse-pdf">
    
      <form method="post" action="{{route('laporan.laporansaldoamal.cetak')}}" id="frm-pdf" target="_blank">
      {{csrf_field()}}
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          <div class="form-group">
          <label>Tanggal Awal ?</label>
          <input type="date" name="Tanggal_Awal" class="form-control">
		  </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          <div class="form-group">
          <label>Tanggal Akhir ?</label>
          <input type="date" name="Tanggal_Akhir" class="form-control">
		  </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <button type="submit" class="btn btn-success btn-block btn-flat">Export to PDF <i class="fa fa-file-pdf-o"></i></button>
		  <br>
		</div>
      </form>
	  </div>
	  <ol class="breadcrumb">
        <li><a href="{{ url('/home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Laporan Transaksi Saldo Amal</li>
    </ol>
</section>


@endsection

@section('css')
<link rel="stylesheet" href="/vendor/datatables/dataTables.bootstrap.css">
<link rel='stylesheet' href="/vendor/datatables/extensions/Responsive/css/dataTables.responsive.css">
@endsection

@section('js')
<script src="/vendor/datatables/jquery.dataTables.min.js"></script>
<script src="/vendor/datatables/dataTables.bootstrap.min.js"></script>
<script src="/vendor/datatables/extensions/Responsive/js/dataTables.responsive.min.js"></script>

@endsection

