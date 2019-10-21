<div class="modal fade " id="herobi-modal" tabindex="-1" role="dialog" aria-labelledby="modal-modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Default userdal Tittle</h4>
            </div>
            <div class="modal-body">
                
                <div class="form-group">
                    <label for="code">Provinsi</label>
                    <input type="text" class="form-control" id="province" required autofocus>
                    <input type="hidden" id='province-id'>
                </div>
                
                <div class="form-group">
                    <label for="code">Kota</label>
                    <input type="text" class="form-control" id="city" required>
                    <input type="hidden" id='city-id'>
                </div>

                <div class="form-group">
                    <label for="name">Kecamatan</label>
                    <input type="text" class="form-control" id="subdistrict" required>
                    <input type="hidden" id="subdistrict-id">
                </div>

                <div class="form-group">
                    <label for="name">Kelurahan</label>
                    <input type="text" class="form-control" id="kelurahan" required>
                    <input type="hidden" id="kelurahan-id">
                </div>
                
                <div class="form-group">
                    <label for="name">Kode Pos</label>
                    <input type="text" class="form-control" id="kodepos" disabled required>
                </div>

                <div class="form-group">
                    <label for="name">Alamat</label>
                    <textarea type="text" class="form-control" id="alamat" required></textarea>
                </div>

            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Tutup</button>
                <button class="btn btn-success hide" type="button" id='create-herobi-modal'>Tambah Alamat</button>
                <button class="btn btn-success hide" type="button" id='edit-herobi-modal'>Ubah Alamat</button>
            </div>
        </div>
    </div>
</div>