<div class="form-group {!! $errors->has('suplier_id') ? 'has-error' : '' !!}">
	{!! Form::label('suplier_id', 'Suplier', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::select('suplier_id',[''=>'']+App\Model\Suplier::pluck('nama_suplier','id')->all(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih Suplier']) !!}
		{!! $errors->first('suplier_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('gudang_id') ? 'has-error' : '' !!}">
	{!! Form::label('gudang_id', 'Gudang', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::select('gudang_id',[''=>'']+App\Model\Gudang::pluck('nama_gudang','id')->all(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih Gudang']) !!}
		{!! $errors->first('gudang_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('tanggal_po') ? 'has-error' : '' !!}">
	{!! Form::label('tanggal_po', 'Tanggal Purchasing Order', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::date('tanggal_po', null, ['class'=>'form-control']) !!}
		{!! $errors->first('tanggal_po', '<p class="help-block">:message</p>') !!}
	</div>
</div>

<div class="form-group {!! $errors->has('tanggal_po') ? 'has-error' : '' !!}">
	{!! Form::label('barang', "Suplier Barang", ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::select('suplier_barang',[''=>''], null,['class'=>'combobox js-selectize custom-suplier-barang', 'placeholder'=>'Pilih Barang', 'id'=>"suplier_barang", 'onchange'=>"updateHarga()"]) !!}
		{!! $errors->first('tanggal_po', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('tanggal_po') ? 'has-error' : '' !!}">
	{!! Form::label('barang_conversi', 'Satuan', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::select('barang_conversi',[''=>'']+App\Model\BarangConversi::list(), null,['class'=>'combobox js-selectize', 'placeholder'=>'Pilih Satuan', 'id'=>'barang_conversi']) !!}
		{!! $errors->first('tanggal_po', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('tanggal_po') ? 'has-error' : '' !!}">
	{!! Form::label('jumlah', 'Jumlah', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::number('jumlah', null, ['class'=>'form-control', 'placeholder' => 'Input Jumlah', 'id'=>"jumlah", 'onkeyup'=>"updateHarga()"]) !!}
		{!! $errors->first('tanggal_po', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('tanggal_po') ? 'has-error' : '' !!}">
	{!! Form::label('harga', 'Harga', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('harga', null, ['class'=>'form-control', 'readonly', 'id'=>"harga"]) !!}
		{!! $errors->first('tanggal_po', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-actions">
	<div class="row">
		<div class="col-md-offset-2 col-md-10">
			{!! Form::button('Add', ['class'=>'btn green-haze', 'id'=>'add']) !!}
		</div>
	</div>
</div>

<table class="table table-striped table-bordered table-advance table-hover" id="custome-table">
    <thead>
        <tr>
            <th>
               Barang 
            </th>
            <th>
                Satuan
            </th>
            <th>
                Jumlah
            </th>
            <th>
                Total Harga
            </th>
            <th>
            </th>
        </tr>
    </thead>
    <tbody class="new-row">

    </tbody>
    <tfoot>
        <tr>
            <td colspan="3"><b>Total Belanja</b></td>
            <td colspan="3"><input type="text" class="form-control" name="total_belanja" id="total_belanja" readonly style="text-align:right;font-weight:bold"></td>
        </tr>
    </tfoot>
</table>

@section('scripts')
<script>
$(document).ready(function(){

	var total_belanja   = 0;

	function clear(){
		$("#jumlah").val('');
		$("#harga").val('Rp.0');
	}
	
	function selectizeme(){
		$('.combobox').selectize({
			create: true,
			sortField: 'text'
		});
	}

	function selectizeLast(){
		$('.combobox').selectize({
			create: false,
			sortField: 'text'
		});
		var elements = document.getElementsByName("suplier_barang[]");
		var selectize_data = elements[elements.length - 1].selectize;
	}

	$('select[name="suplier_id"]').change(function(){
		{{ $link = url('/suplierbarang/listbarang/') }}
		suplier_id = $('select[name="suplier_id"]').val();

		$.ajax({
			url: "{{ $link }}/"+$('select[name="suplier_id"]').val(),
			type: 'GET',
			success: function(respon) {
				var selectize_data = $("#suplier_barang")[0].selectize;
					selectize_data.clearOptions();
				var data = jQuery.parseJSON(respon);
				for (var i = 0; i < data.hasil.length; i++) {
					selectize_data.addOption({
						text:data.hasil[i].nama,
						value:data.hasil[i].id
					});
					selectize_data.refreshOptions() ;
				}
			},
		});
	});

	function setupSelect(){
		var elements = document.getElementsByName("suplier_barang[]");
		for(var i=0; i<elements.length; i++) {
			var selectize_data = elements[i].selectize
			selectize_data.clearOptions();
			selectize_data.addOption(dataSelect);
			selectize_data.refreshOptions();
		}
	}

	$(document).on('click','#add',function(){
	    var label_suplier_barang	= $("#suplier_barang").text();
	    var suplier_barang	= $("#suplier_barang").val();

	    var label_barang_conversi	= $("#barang_conversi").text();
	    var barang_conversi	= $("#barang_conversi").val();

	    var jumlah	= $("#jumlah").val();
	    var harga	= $("#harga").val();

		if(suplier_barang  == ""){
			swal('Peringatan','Barang tidak boleh kosong.','warning');
			return false;
		}

		if(barang_conversi  == ""){
			swal('Peringatan','Satuan tidak boleh kosong.','warning');
			return false;
		}

	    if(jumlah.length < 1){
	        swal('Peringatan','Jumlah tidak boleh kosong.','warning');
	        return false;
	    }

	    var markup  = "<tr id='list'>"+
	            "<td width='30%'>"+
	            "<input type='text' class='form-control' value='"+label_suplier_barang+"' readonly>"+
	            "<input type='hidden' class='form-control' name='suplier_barang[]' value='"+suplier_barang+"' readonly>"+
	            "</td>"+
	            "<td>"+
	            "<input type='text' class='form-control' value='"+label_barang_conversi+"' readonly>"+
	            "<input type='hidden' class='form-control' name='barang_conversi[]' value='"+barang_conversi+"' readonly>"+
	            "</td>"+
	            "<td>"+
	            "<input type='text' class='form-control' name='jumlah[]' value='"+jumlah+"' readonly>"+
	            "</td>"+
	            "<td>"+
	            "<input type='text' class='form-control' value='"+harga+"' name='harga[]' id='total' readonly>"+
	            "</td>"+
	            "<td>"+
	            "<button class='btn btn-danger' id='remove' title='Remove Row'>X</button>"+
	            "</td>"+
	            "</tr>";

	    $("#custome-table tbody").append(markup);

		harga = harga.replace('Rp.','');
		harga = harga.replace('.','');
		total_belanja = parseInt(total_belanja) + parseInt(harga);
		console.log('total_belanja:'+total_belanja);

		var tes= formatRupiah(total_belanja, 'Rp.');
		console.log('tes:'+tes);

	    $("#total_belanja").val(formatRupiah(total_belanja, 'Rp.'));

	    clear();
	});

	$(document).on('click','#remove',function(){
	    var current = $(this).closest("#list").find("#total").val();
		current = current.replace('Rp.','');
		current = current.replace('.','');

	    $(this).parents('tr').remove();

	    total_belanja = parseInt(total_belanja) - parseInt(current);

	    $("#total_belanja").val(formatRupiah(total_belanja, 'Rp.'));
	});
	

});

function updateHarga(){
	var suplierbarang = $('#suplier_barang').text();
	if(suplierbarang == ""){
		alert('Pilih Suplier Barang Terlebih Dahulu');
		$("#jumlah").val("");
		return;
	}

	suplierbarang = suplierbarang.replace(" )", "").split('Rp.')[1];

	var harga = suplierbarang.replace(".", "").replace(".", "").replace(".", "").replace(".", "").replace(".", "");
	var jumlah = $("#jumlah").val();
	// console.log(harga);
	// console.log(jumlah);
	$("#harga").val(formatRupiah(jumlah*harga, 'Rp.'));
}

/* Fungsi formatRupiah */
function formatRupiah(angka, prefix){
	var number_string = angka.toString(),
	split   		= number_string.split(','),
	sisa     		= split[0].length % 3,
	rupiah     		= split[0].substr(0, sisa),
	ribuan     		= split[0].substr(sisa).match(/\d{3}/gi);

	// tambahkan titik jika yang di input sudah menjadi angka ribuan
	if(ribuan){
		separator = sisa ? '.' : '';
		rupiah += separator + ribuan.join('.');
	}

	rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
	return prefix == undefined ? rupiah : (rupiah ? 'Rp.' + rupiah : '');
}
</script>
@endsection
