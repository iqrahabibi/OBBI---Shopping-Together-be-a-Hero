<div class="modal fade " id="province-modal" tabindex="-1" role="dialog" aria-labelledby="mo-modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modal-title">List province</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover" id='tbl_province' width="100%">
                    <thead>
                        <tr>
                            <th width='5%'>No.</th>
                            <th>Nama Province</th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Tutup</button>
                <button id="province-modal-clear" class="btn btn-info" type="button">Clear</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function()
    {
        var ajaxprovincetable   = '/modal/province';
        
        var tableprovince   = $("#tbl_province").DataTable({
            select : true,
            language :{
                url : "/datatables-indonesia.json"
            },
            processing : true,
            serverSide : true,
            ajax: {
                url         : ajaxprovincetable,
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
                    targets     : 2,
                    className   : "text-center",
                    visible  :false
                }
            ]
        });
    
        $( window ).resize(function() 
        {
            tableprovince.responsive.recalc();
        });
    
        $('#tbl_province').on('page.dt', function () 
        {
            var page = tableprovince.page.info().page + 1;
            tableprovince.ajax.url(ajaxprovincetable + "?page=" + page);
    
            $('#tbl_province').on('order.dt', function () {
                tableprovince.ajax.url(ajaxprovincetable + "?page=1");
            });
        });
    
        $('#tbl_province').on('length.dt', function () 
        {
            tableprovince.ajax.url(ajaxprovincetable + "?page=1");
        });
    
        $('#tbl_province').on('search.dt', function () 
        {
            if(tableprovince.search( this.value ) !== ''){
                tableprovince.ajax.url(ajaxprovincetable + "?page=1");
            }
        });
    
        $(document).on("keydown", "#province", function()
        {
            return false;
        });
    
        $(document).on("click", "#province", function(e)
        {
            tableprovince.ajax.url(ajaxprovincetable);
            tableprovince.ajax.reload();
            
            $("#modal-title").html('List Province');
            $("#province-modal").modal({
                backdrop: 'static'
            });
        });
        
        $("#tbl_province tbody").on("click", "td", function()
        {
            var current_row = $(this).parents('tr');
            if (current_row.hasClass('child')) {
                current_row = current_row.prev();
            }
            var data = tableprovince.row(current_row).data();
    
            $("#province").val(data[1]);
            $("#province-id").val(data[2]);
            $("#province-modal").modal('hide');
        });
    
        $("#province-modal-clear").on("click", function()
        {
            $("#province").val('');
            $("#province-id").val('');
            $("#city").val('');
            $("#city-id").val('');
            $("#subdistrict").val('');
            $("#subdistrict-id").val('');
            $("#kelurahan").val('');
            $("#kelurahan-id").val('');
            $("#province-modal").modal('hide');
        });
    
    });
</script>