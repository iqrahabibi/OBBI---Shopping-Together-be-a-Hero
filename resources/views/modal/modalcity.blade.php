<div class="modal fade " id="city-modal" tabindex="-1" role="dialog" aria-labelledby="mo-modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">List city</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover" id='tbl_city' width="100%">
                    <thead>
                        <tr>
                            <th width='5%'>No.</th>
                            <th>Tipe</th>
                            <th>Nama City</th>
                            <th>Province</th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Tutup</button>
                <button id="city-modal-clear" class="btn btn-info" type="button">Clear</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function()
    {
        var ajaxcitytable   = '/modal/city';
        var province        = "";

        var tablecity   = $("#tbl_city").DataTable({
            select : true,
            language :{
                url : "/datatables-indonesia.json"
            },
            processing : true,
            serverSide : true,
            ajax: {
                url         : ajaxcitytable+"?province="+province,
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
            tablecity.responsive.recalc();
        });
    
        $('#tbl_city').on('page.dt', function () 
        {
            var page = tablecity.page.info().page + 1;
            tablecity.ajax.url(ajaxcitytable + "?page=" + page+"&province="+province);
    
            $('#tbl_city').on('order.dt', function () {
                tablecity.ajax.url(ajaxcitytable + "?page=1&province="+province);
            });
        });
    
        $('#tbl_city').on('length.dt', function () 
        {
            tablecity.ajax.url(ajaxcitytable + "?page=1&province="+province);
        });
    
        $('#tbl_city').on('search.dt', function () 
        {
            if(tablecity.search( this.value ) !== ''){
                tablecity.ajax.url(ajaxcitytable + "?page=1&province="+province);
            }
        });
    
        $(document).on("keydown", "#city", function()
        {
            return false;
        });
    
        $(document).on("click", "#city", function(e)
        {
            if($("#province").val() == ""){
                swal('Perhatian','Province tidak boleh kosong','warning');
                return false;
            }
            province   = $("#province-id").val();
            
            tablecity.ajax.url(ajaxcitytable+"?province="+province);
            tablecity.ajax.reload();
    
            $("#city-modal").modal({
                backdrop: 'static'
            });
        });
        
        $("#tbl_city tbody").on("click", "td", function()
        {
            var current_row = $(this).parents('tr');
            if (current_row.hasClass('child')) {
                current_row = current_row.prev();
            }
            var data = tablecity.row(current_row).data();
    
            $("#city").val(data[2]);
            $("#city-id").val(data[4]);
            $("#city-modal").modal('hide');
        });
    
        $("#city-modal-clear").on("click", function()
        {
            $("#city").val('');
            $("#city-id").val('');
            $("#subdistrict").val('');
            $("#subdistrict-id").val('');
            $("#kelurahan").val('');
            $("#kelurahan-id").val('');
            $("#city-modal").modal('hide');
        });
    
    });
</script>