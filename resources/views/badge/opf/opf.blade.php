@extends('layouts.admin_layots')

@section('content')
<section class="content-header">
    <h1>
        Badge OPF
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ url('/home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">List OPF</li>
    </ol>
</section>

<section class="content">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title">Data OPF</h3>
        </div>
        <div class="panel-body">
            <div class="col-md-12 table-responsive">
                <button class="btn btn-primary" id="add-opf"><i class="fa fa-plus"></i> Tambah OPF</button>
                <br><br>
                <table class="table table-bordered table-hover" id="tbl_opf" width="100%">
                    <thead>
                        <th width="5%">No.</th>
                        <th>Nama User</th>
                        <th>Kode Referal</th>
                        <th>Status</th>
						<th>Gambar</th>
                        <th>Tindakan</th>
                        <th>Id</th>
                        <th>Updated_at</th>
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

        var urlOpf      = "/badge/opf/read";

        var tableOpf    = $("#tbl_opf").DataTable({
            select :true,
            responsive : true,
            processing : true,
            serverSide : true,
            ajax : {
                url : urlOpf,
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
                    className : 'never',
                    visible : false,
                },
                {
                    targets : 7,
                    className : 'never',
                    visible : false,
                }
            ]
        });

        $('#tbl_opf').on('page.dt', function () 
        {
            var page = tableOpf.page.info().page + 1;
            tableOpf.ajax.url(urlOpf + "?page=" + page);
    
            $('#tbl_opf').on('order.dt', function () {
                tableOpf.ajax.url(urlOpf + "?page=1");
            });
        });
    
        $('#tbl_opf').on('length.dt', function ( e, settings, len ) 
        {
            tableOpf.ajax.url(urlOpf + "?page=1");
        });
    
        $('#tbl_opf').on('search.dt', function () 
        {
            if(tableOpf.search( this.value ) !== ''){
                tableOpf.ajax.url(urlOpf + "?page=1");
            }
        });

        $(document).on('click','#add-opf',function() {
            $(".modal-title").html('Tambah User OPF');

            $("#opf-modal").modal({
                backdrop : 'static'
            });
        });

        $(document).on('click','#create-opf-modal',function(){
			var image   = $("#image").prop('files')[0];
            var email   = $("#email").val();
            var phone   = $("#phone").val();

            if(email.length < 1){
                swal('Perhatian','E-mail tidak boleh kosong.','warning');

                return false;
            }

            if(phone.length < 5){
                swal('Perhatian','Nomor telepon tidak sesuai.','warning');

                return false;
            }

            var formData = new FormData();
            formData.append('image',image);
            formData.append('phone',phone);
            formData.append('email',email);

            swal({
                title : "",
                text : "Tambah user OPF ?",
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
                    url : "{{ route('badge.opf.create')}}",
                    data : formData,
                    contentType: false,
                    processData: false,
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
                        
                        $("#opf-modal").modal('hide');
                        tableOpf.ajax.reload();
                    }
                });
            });
        });

        $("#tbl_opf tbody").on('click','#ubah-status',function(){
            var current_row = $(this).parents('tr');
            if (current_row.hasClass('child')) {
                current_row = current_row.prev();
            } 
            var data = tableOpf.row(current_row).data();

            id  = data[5];
            updated_at  = data[6];
            var status  = data[3];

            swal({
                title : "",
                text : "Ubah status user OPF?",
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
                    url : "{{ route('badge.opf.grant')}}",
                    data : {opf_id:id,status:status},
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
    
                        tableOpf.ajax.reload();
                        $("#opf-modal").modal('hide');
                    }
                });
            });
        });

        $("#opf-modal").on("hidden.bs.modal", function(){
            $("#email").val('');
        });
    });
</script>

@endsection

@section('modal')
@include('badge.opf.modalopf')
@endsection