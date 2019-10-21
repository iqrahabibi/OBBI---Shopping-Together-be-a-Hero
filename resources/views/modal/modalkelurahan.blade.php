<div class="modal fade " id="kelurahan-modal" tabindex="-1" role="dialog" aria-labelledby="mo-modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">List kelurahan</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover" id='tbl_kelurahan' width="100%">
                    <thead>
                        <tr>
                            <th width='5%'>No.</th>
                            <th>Nama kelurahan</th>
                            <th>Kode Pos</th>
                            <th>Kecamatan</th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Tutup</button>
                <button id="kelurahan-modal-clear" class="btn btn-info" type="button">Clear</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function()
    {
        var ajaxkelurahantable   = '/modal/kelurahan';
        var subdistrict        = "";

        var tablekelurahan   = $("#tbl_kelurahan").DataTable({
            select : true,
            language :{
                url : "/datatables-indonesia.json"
            },
            processing : true,
            serverSide : true,
            ajax: {
                url         : ajaxkelurahantable+"?subdistrict="+subdistrict,
                type        : 'POST',
                headers     :   {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
            },
            columnDefs  :   [
                {
                    targets : 0,
                    className : "text-center",
                    orderable : false
                },
                {
                    targets : 1,
                    className : "text-center"
                },
                {
                    targets : 2,
                    className : "text-center"
                },
                {
                    targets : 3,
                    className : "text-center"
                },
                {
                    targets     : 4,
                    className   : "text-center",
                    visible  :false
                }
            ]
        });
    
        $( window ).resize(function() 
        {
            tablekelurahan.responsive.recalc();
        });
    
        $('#tbl_kelurahan').on('page.dt', function () 
        {
            var page = tablekelurahan.page.info().page + 1;
            tablekelurahan.ajax.url(ajaxkelurahantable + "?page=" + page+"&subdistrict="+subdistrict);
    
            $('#tbl_kelurahan').on('order.dt', function () {
                tablekelurahan.ajax.url(ajaxkelurahantable + "?page=1&subdistrict="+subdistrict);
            });
        });
    
        $('#tbl_kelurahan').on('length.dt', function () 
        {
            tablekelurahan.ajax.url(ajaxkelurahantable + "?page=1&subdistrict="+subdistrict);
        });
    
        $('#tbl_kelurahan').on('search.dt', function () 
        {
            if(tablekelurahan.search( this.value ) !== ''){
                tablekelurahan.ajax.url(ajaxkelurahantable + "?page=1&subdistrict="+subdistrict);
            }
        });
    
        $(document).on("keydown", "#kelurahan", function()
        {
            return false;
        });
    
        $(document).on("click", "#kelurahan", function(e)
        {
            if($("#subdistrict").val() == ""){
                swal('Perhatian','subdistrict tidak boleh kosong','warning');
                return false;
            }
            subdistrict   = $("#subdistrict-id").val();
            
            tablekelurahan.ajax.url(ajaxkelurahantable+"?subdistrict="+subdistrict);
            tablekelurahan.ajax.reload();
    
            $("#kelurahan-modal").modal({
                backdrop: 'static'
            });
        });
        
        $("#tbl_kelurahan tbody").on("click", "td", function()
        {
            var current_row = $(this).parents('tr');
            if (current_row.hasClass('child')) {
                current_row = current_row.prev();
            }
            var data = tablekelurahan.row(current_row).data();
    
            $("#kelurahan").val(data[1]);
            $("#kodepos").val(data[2]);
            $("#kelurahan-id").val(data[4]);
            $("#kelurahan-modal").modal('hide');
        });
    
        $("#kelurahan-modal-clear").on("click", function()
        {
            $("#kelurahan").val('');
            $("#kelurahan-id").val('');
            $("#kodepos").val('');
            $("#kelurahan-modal").modal('hide');
        });
    
    });
</script>