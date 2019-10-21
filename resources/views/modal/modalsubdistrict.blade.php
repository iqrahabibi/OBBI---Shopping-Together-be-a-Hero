<div class="modal fade " id="subdistrict-modal" tabindex="-1" role="dialog" aria-labelledby="mo-modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">List subdistrict</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover" id='tbl_subdistrict' width="100%">
                    <thead>
                        <tr>
                            <th width='5%'>No.</th>
                            <th>Nama Subdistrict</th>
                            <th>City</th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Tutup</button>
                <button id="subdistrict-modal-clear" class="btn btn-info" type="button">Clear</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function()
    {
        var ajaxsubdistricttable   = '/modal/subdistrict';
        var city        = "";

        var tablesubdistrict   = $("#tbl_subdistrict").DataTable({
            select : true,
            language :{
                url : "/datatables-indonesia.json"
            },
            processing : true,
            serverSide : true,
            ajax: {
                url         : ajaxsubdistricttable+"?city="+city,
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
                    targets     : 3,
                    className   : "text-center",
                    visible  :false
                }
            ]
        });
    
        $( window ).resize(function() 
        {
            tablesubdistrict.responsive.recalc();
        });
    
        $('#tbl_subdistrict').on('page.dt', function () 
        {
            var page = tablesubdistrict.page.info().page + 1;
            tablesubdistrict.ajax.url(ajaxsubdistricttable + "?page=" + page+"&city="+city);
    
            $('#tbl_subdistrict').on('order.dt', function () {
                tablesubdistrict.ajax.url(ajaxsubdistricttable + "?page=1&city="+city);
            });
        });
    
        $('#tbl_subdistrict').on('length.dt', function () 
        {
            tablesubdistrict.ajax.url(ajaxsubdistricttable + "?page=1&city="+city);
        });
    
        $('#tbl_subdistrict').on('search.dt', function () 
        {
            if(tablesubdistrict.search( this.value ) !== ''){
                tablesubdistrict.ajax.url(ajaxsubdistricttable + "?page=1&city="+city);
            }
        });
    
        $(document).on("keydown", "#subdistrict", function()
        {
            return false;
        });
    
        $(document).on("click", "#subdistrict", function(e)
        {
            if($("#city").val() == ""){
                swal('Perhatian','city tidak boleh kosong','warning');
                return false;
            }
            city   = $("#city-id").val();
            
            tablesubdistrict.ajax.url(ajaxsubdistricttable+"?city="+city);
            tablesubdistrict.ajax.reload();
    
            $("#subdistrict-modal").modal({
                backdrop: 'static'
            });
        });
        
        $("#tbl_subdistrict tbody").on("click", "td", function()
        {
            var current_row = $(this).parents('tr');
            if (current_row.hasClass('child')) {
                current_row = current_row.prev();
            }
            var data = tablesubdistrict.row(current_row).data();
    
            $("#subdistrict").val(data[1]);
            $("#subdistrict-id").val(data[3]);
            $("#subdistrict-modal").modal('hide');
        });
    
        $("#subdistrict-modal-clear").on("click", function()
        {
            $("#subdistrict").val('');
            $("#subdistrict-id").val('');
            $("#kelurahan").val('');
            $("#kelurahan-id").val('');
            $("#subdistrict-modal").modal('hide');
        });
    
    });
</script>