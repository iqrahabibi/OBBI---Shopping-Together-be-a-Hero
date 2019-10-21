<div class="modal fade " id="saldo-modal" tabindex="-1" role="dialog" aria-labelledby="saldo-modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <div class='row' style='margin: 0'>
                    <div class='col-md-12'>
                        <div class="form-group">
                            <label>Kode Unik</label>
                            <input type="text" class="form-control" id="code" disabled>
                        </div>
                        <div class="form-group">
                            <label>Nama User</label>
                            <input type="text" class="form-control" id="fullname" disabled>
                        </div>
                        <div class="form-group">
                            <label>Balance</label>
                            <input type="text" class="form-control" id="balance">
                        </div>
                        <div class="form-group">
                            <label>Note</label>
                            <input type="text" class="form-control" id="notes">
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="update">Update</button>
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
            </div>
            <div id='dim-light'></div>
        </div>
    </div>
</div>