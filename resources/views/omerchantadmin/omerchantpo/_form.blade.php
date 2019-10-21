<div class="form-group {!! $errors->has('kode') ? 'has-error' : '' !!}">
	{!! Form::label('kode', 'Omerchant', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::select('kode',[''=>'']+App\Model\UsahaOMerchant::list_for_omerchant_admin(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih Usaha OMerchant']) !!}
		{!! $errors->first('kode', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('tanggal') ? 'has-error' : '' !!}">
	{!! Form::label('tanggal', 'Tanggal Purchasing Order', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::date('tanggal', null, ['class'=>'form-control']) !!}
		{!! $errors->first('tanggal', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('barang') ? 'has-error' : '' !!}">
	{!! Form::label('barang', 'Barang', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::select('barang',[''=>'']+App\Model\BarangGrosir::list(), null,['class'=>'combobox js-selectize', 'placeholder'=>'Pilih Barang', 'id'=>"barang", 'onchange'=>"updateHarga()"]) !!}
		{!! $errors->first('barang', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('jumlah') ? 'has-error' : '' !!}">
	{!! Form::label('jumlah', 'Jumlah', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::number('jumlah', null, ['class'=>'form-control', 'placeholder' => 'Input Jumlah', 'id'=>"jumlah", 'onkeyup'=>"updateHarga()"]) !!}
		{!! $errors->first('jumlah', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('harga') ? 'has-error' : '' !!}">
	{!! Form::label('harga', 'Harga', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::text('harga', null, ['class'=>'form-control', 'readonly', 'id'=>"harga"]) !!}
		{!! $errors->first('harga', '<p class="help-block">:message</p>') !!}
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
            <th width="60%">
               Barang 
            </th>
            <th width="15%">
                Jumlah
            </th>
            <th width="15%">
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
            <td colspan="2"><b>Total Belanja</b></td>
            <td colspan="2"><input type="text" class="form-control" name="total_belanja" id="total_belanja" readonly style="text-align:right;font-weight:bold"></td>
        </tr>
    </tfoot>
</table>

@section('scripts')
<script>
$(document).ready(function () {

	var total_belanja   = 0;

	function clear(){
		$("#jumlah").val('');
		$("#harga").val('Rp.0');
	}

	$(document).on('click','#add',function(){
		var label_barang	= $("#barang").text();
		var barang	= $("#barang").val();

		var jumlah	= $("#jumlah").val();
		var harga	= $("#harga").val();

		if(label_barang  == ""){
			swal('Peringatan','Barang tidak boleh kosong.','warning');
			return false;
		}

		if(jumlah.length < 1){
			swal('Peringatan','Jumlah tidak boleh kosong.','warning');
			return false;
		}

		var grosir = label_barang.split('Pembelian ')[1].split(' ')[0];
		if(jumlah % grosir != 0){
			swal('Peringatan','Jumlah tidak sesuai grosir yang tersedia.','warning');
			return false;
		}

		var markup  = "<tr id='list'>"+
						"<td>"+
							"<input type='text' class='form-control' value='"+label_barang+"' readonly>"+
							"<input type='hidden' class='form-control' name='barang[]' value='"+barang+"' readonly>"+
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
		harga = harga.replace('.','').replace('.','').replace('.','').replace('.','').replace('.','');
		total_belanja = parseInt(total_belanja) + parseInt(harga);
		console.log('total_belanja:'+total_belanja);

		$("#total_belanja").val(formatRupiah(total_belanja, 'Rp.'));

		clear();
	});

	$(document).on('click','#remove',function(){
		var current = $(this).closest("#list").find("#total").val();
		current = current.replace('Rp.','');
		current = current.replace('.','').replace('.','').replace('.','').replace('.','').replace('.','');

		$(this).parents('tr').remove();

		total_belanja = parseInt(total_belanja) - parseInt(current);

		$("#total_belanja").val(formatRupiah(total_belanja, 'Rp.'));
	});
	
	var dataSelect = null;

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
				// var data = jQuery.parseJSON(JSON.stringify(respon));
				var data = jQuery.parseJSON(respon);
				dataSelect = [];
				for (var i = 0; i < data.hasil.length; i++) {
					dataSelect.push({
						text:data.hasil[i].nama,
						value:data.hasil[i].id
					});
				}
				setupSelect();
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
});

function updateHarga(){
	var barang = $('#barang').text();
	if(barang == ""){
		alert('Pilih Barang Terlebih Dahulu');
		return;
	}

	barang = barang.replace(" )", "").split('Rp.')[1];
	barang = barang.replace(".", "").replace(".", "").replace(".", "").replace(".", "").replace(".", "");
	
	var harga = barang.split('/pcs')[0];
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
