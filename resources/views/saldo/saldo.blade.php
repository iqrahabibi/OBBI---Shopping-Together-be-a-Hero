@extends('layouts.admin_layots')

@section('content')
<section class="content-header">
    <h1>
        List Saldo
        <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ url('/home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">List Saldo</li>
    </ol>
</section>

<section class="content">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title">Data Saldo</h3>
        </div>
        <div class="panel-body">
            <div class="col-md-12 table-responsive">
                <table class="table table-bordered table-hover" id="tbl_saldo" width="100%">
                    <thead>
                        <th width="5%">No.</th>
                        <th>Nama User</th>
                        <th>Balance</th>
                        <th>Kode Unik</th>
                        <th>Note</th>
                        <th>Status</th>
                        <th>Tindakan</th>
                        <th>Id</th>
                        <th>Updated_at</th>
                        <th>Id User</th>
                        <th>Valid</th>
                    </thead>
                </table>
            </div>
        </div>
    </div>
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

<script>
    $(document).ready(function(){
        var id          = "";
        var updated_at  = "";
        var id_user     = "";

        var urlsaldo = '/saldo/tampildata';

        var tablesaldo   = $("#tbl_saldo").DataTable({
            select :true,
            responsive : true,
            processing : true,
            serverSide : true,
            ajax : {
                url : urlsaldo,
                type : "POST",
                headers     :   {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
            },
            columnDefs : [
                {
                    targets : 0,
                    className : 'text-center',
                    orderable : false
                },
                {
                    targets : 1,
                    className : 'text-center',
                },
                {
                    targets : 2,
                    className : 'text-center'
                },
                {
                    targets : 3,
                    className : 'text-center',
                    orderable : false
                },
                {
                    targets : 4,
                    className : 'text-center',
                    orderable : false,
                },
                {
                    targets : 5,
                    className : 'text-center',
                    orderable : false,
                },
                {
                    targets : 6,
                    className : 'text-center',
                    orderable : false,
                },
                {
                    targets : 7,
                    className : 'never',
                    visible : false,
                },
                {
                    targets : 8,
                    className : 'never',
                    visible : false,
                },
                {
                    targets : 9,
                    className : 'never',
                    visible : false,
                },
                {
                    targets : 10,
                    className : 'never',
                    visible : false,
                }
            ]
        });
    
        $('#tbl_saldo').on('page.dt', function () 
        {
            var page = tablesaldo.page.info().page + 1;
            tablesaldo.ajax.url(urlsaldo + "?page=" + page);
    
            $('#tbl_saldo').on('order.dt', function () {
                tablesaldo.ajax.url(urlsaldo + "?page=1");
            });
        });
    
        $('#tbl_saldo').on('length.dt', function ( e, settings, len ) 
        {
            tablesaldo.ajax.url(urlsaldo + "?page=1");
        });
    
        $('#tbl_saldo').on('search.dt', function () 
        {
            if(tablesaldo.search( this.value ) !== ''){
                tablesaldo.ajax.url(urlsaldo + "?page=1");
            }
        });

        $("#tbl_saldo tbody").on('click','#ubah-status',function()
        {
            var current_row = $(this).parents('tr');
            if (current_row.hasClass('child')) {
                current_row = current_row.prev();
            } 
            var data = tablesaldo.row(current_row).data();

            id          = data[7];
            updated_at  = data[8];
            var valid   = data[10];

            swal({
                title : "",
                text : "Ubah status saldo pada user "+data[1]+" ?",
                type : "warning",
                showCancelButton: true,					
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                allowOutsideClick: false,
                reverseButtons: true,
                buttonsStyling: true
            }).then(function(event){
                swal({
                    title : "",
                    text : "Harap Tunggu",
                    type : "info",
                    showCancelButton: false,
                    showConfirmButton : false,
                    allowEscapeKey:false,
                    allowOutsideClick: false
                });

                jQuery.ajax({
                    url : "{{ route('saldo.valid')}}",
                    data : {id:id,updated_at:updated_at,valid:valid},
                    type : "POST",
                    dataType : 'json',
                    headers : {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success : function(e){
                        swal({
                            title : e['title'],
                            text : e['text'],
                            type : e['type'],				
                            confirmButtonText: "Ok",
                            allowOutsideClick: false,
                            reverseButtons: true,
                            buttonsStyling: true
                        });
    
                        tablesaldo.ajax.reload();
                    }
                });
            });
        });

        $('#tbl_saldo tbody').on('click','#edit-saldo',function(){
            var current_row = $(this).parents('tr');
            if (current_row.hasClass('child')) {
                current_row = current_row.prev();
            } 
            var data = tablesaldo.row(current_row).data();
            
            id          = data[7];
            updated_at  = data[8];
            id_user     = data[9];

            $("#code").val(data[3]);
            $("#fullname").val(data[1]);
            $("#balance").val(data[2]);
            $("#notes").val(data[4]);

            $(".modal-title").html('Edit Saldo '+data[7]);

            $("#saldo-modal").modal({
                backdrop : 'static'
            });
        });

        $(document).on("click","#update",function(){
            var fullname    = $("#fullname").val();
            var balance     = $("#balance").val();
            var code        = $("#code").val();
            var notes       = $("#notes").val();

            swal({
                title : "",
                text : "Ubah saldo?",
                type : "warning",
                showCancelButton: true,					
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                allowOutsideClick: false,
                reverseButtons: true,
                buttonsStyling: true
            }).then(function(event){
                swal({
                    title : "",
                    text : "Harap Tunggu",
                    type : "info",
                    showCancelButton: false,
                    showConfirmButton : false,
                    allowEscapeKey:false,
                    allowOutsideClick: false
                });

                jQuery.ajax({
                    url : "{{ route('saldo.edit')}}",
                    data : {fullname : fullname,code:code,id:id,updated_at:updated_at,balance : balance,notes : notes},
                    type : "POST",
                    dataType : 'json',
                    headers : {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success : function(e){
                        swal({
                            title : e['title'],
                            text : e['text'],
                            type : e['type'],
                            showCancelButton: false,
                            allowOutsideClick: false
                        });
    
                        tablesaldo.ajax.reload();
                        $("#saldo-modal").modal('hide');
                    }
                });
            });
        });

        $('#tbl_saldo tbody').on("click","#delete-saldo",function(){
            var current_row = $(this).parents('tr');
            if (current_row.hasClass('child')) {
                current_row = current_row.prev();
            } 
            var data = tablesaldo.row(current_row).data();
            
            id          = data[7];

            swal({
                title : "",
                text : "Hapus saldo?",
                type : "warning",
                showCancelButton: true,					
                confirmButtonText: "Ya",
                cancelButtonText: "Batal",
                allowOutsideClick: false,
                reverseButtons: true,
                buttonsStyling: true
            }).then(function(event){
                swal({
                    title : "",
                    text : "Harap Tunggu",
                    type : "info",
                    showCancelButton: false,
                    showConfirmButton : false,
                    allowEscapeKey:false,
                    allowOutsideClick: false
                });

                jQuery.ajax({
                    url : "{{ route('saldo.delete')}}",
                    data : {id:id},
                    type : "POST",
                    dataType : 'json',
                    headers : {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success : function(e){
                        swal({
                            title : e['title'],
                            text : e['text'],
                            type : e['type'],
                            showCancelButton: false,
                            allowOutsideClick: false
                        });
    
                        tablesaldo.ajax.reload();
                        $("#saldo-modal").modal('hide');
                    }
                });
            });
        });

        $("#saldo-modal").on("hidden.bs.modal", function(){
            $("#fullname").val('');
            $("#balance").val('');
            $("#code").val('');
            $("#notes").val('');
        });

    });
</script>
@endsection

@section('modal')
@include('saldo.saldomodal')
@endsection