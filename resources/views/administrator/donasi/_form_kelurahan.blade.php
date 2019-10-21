<div class="form-group {!! $errors->has('provinsi_id') ? 'has-error' : '' !!}">
	{!! Form::label('provinsi_id', 'Provinsi', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::select('provinsi_id',[''=>'']+App\Model\Provinsi::pluck('nama_provinsi','id')->all(), null,
			['class'=>'js-selectize', 'placeholder'=>'Pilih Provinsi']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('provinsi_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('kota_id') ? 'has-error' : '' !!}">
	{!! Form::label('kota_id', 'Kabupaten / Kota', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::select('kota_id',[''=>''], null,['class'=>'js-selectize', 'placeholder'=>'Pilih kota / Kota']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('kota_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('kecamatan_id') ? 'has-error' : '' !!}">
	{!! Form::label('kecamatan_id', 'Kecamatan', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::select('kecamatan_id',[''=>''], null,['class'=>'js-selectize', 'placeholder'=>'Pilih Kecamatan']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('kecamatan_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('kelurahan_id') ? 'has-error' : '' !!}">
	{!! Form::label('kelurahan_id', 'Kelurahan', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::select('kelurahan_id',[''=>''], null,['class'=>'js-selectize', 'placeholder'=>'Pilih Kelurahan']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('kelurahan_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('target_donasi_id') ? 'has-error' : '' !!}">
	{!! Form::label('target_donasi_id', 'Target Donasi', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::select('target_donasi_id',[''=>''], null,['class'=>'js-selectize', 'placeholder'=>'Pilih Target Donasi']) !!}
		<div class="form-control-focus"></div>
		{!! $errors->first('target_donasi_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('persentase') ? 'has-error' : '' !!}">
	{!! Form::label('persentase', 'Persentase ( % )', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::number('persentase', null, ['class'=>'form-control']) !!}
		{!! $errors->first('persentase', '<p class="help-block">:message</p>') !!}
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
				Target Donasi
            </th>
            <th>
				Persentase 
            </th>
            <th>
                Jumlah
            </th>
            <th>
            </th>
        </tr>
    </thead>
    <tbody class="new-row">

    </tbody>
    <tfoot>
        <tr>
            <td colspan="2"><b>Total Amal Asik</b></td>
            <td colspan="2"><input type="text" class="form-control" name="total_amal" id="total_amal" readonly style="text-align:right;font-weight:bold"></td>
        </tr>
        <tr>
            <td colspan="2"><b>Total Donasi</b></td>
            <td colspan="2"><input type="text" class="form-control" name="total_donasi" id="total_donasi" readonly style="text-align:right;font-weight:bold"></td>
        </tr>
    </tfoot>
</table>

@section('scripts')
<script>
$(function(){
	$('select[name="provinsi_id"]').change(function(){
		{{ $link = url('/kota/data/') }}
		$.ajax({
			url: "{{ $link }}/"+$('select[name="provinsi_id"]').val(),
			type: 'GET',
			success: function(respon) {
				var selectize_data = $("#kota_id")[0].selectize;
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
	
	$('select[name="kota_id"]').change(function(){
		{{ $link = url('/kecamatan/data/') }}
		$.ajax({
			url: "{{ $link }}/"+$('select[name="kota_id"]').val(),
			type: 'GET',
			success: function(respon) {
				var selectize_data = $("#kecamatan_id")[0].selectize;
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
	
	$('select[name="kecamatan_id"]').change(function(){
		{{ $link = url('/kelurahan/data/') }}
		$.ajax({
			url: "{{ $link }}/"+$('select[name="kecamatan_id"]').val(),
			type: 'GET',
			success: function(respon) {
				var selectize_data = $("#kelurahan_id")[0].selectize;
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
	
	$('select[name="kelurahan_id"]').change(function(){
		$('#total_amal').val('');
		{{ $link = url('/targetdonasi/data/') }}
		$.ajax({
			url: "{{ $link }}/"+$('select[name="kelurahan_id"]').val(),
			type: 'GET',
			success: function(respon) {
				var selectize_data = $("#target_donasi_id")[0].selectize;
					selectize_data.clearOptions();
				var data = jQuery.parseJSON(respon);
				for (var i = 0; i < data.hasil.length; i++) {
					selectize_data.addOption({
						text:data.hasil[i].nama,
						value:data.hasil[i].id
					});
					selectize_data.refreshOptions() ;
				}

				$('#total_amal').val(data.total);
			},
		});
	});
});
$(document).ready(function(){
	var total_donasi   = 0;
	var total_persentase   = 0;

	function clear(){
		$("#persentase").val('');
	}

	$(document).on('click','#add',function(){
	    var label_target_donasi	= $("#target_donasi_id").text();
	    var target_donasi	= $("#target_donasi_id").val();

	    var total_amal	= $("#total_amal").val();
	    var persentase	= $("#persentase").val();
		var jumlah = (total_amal * persentase) / 100;

		if(target_donasi  == ""){
			swal('Peringatan','Target Donasi tidak boleh kosong.','warning');
			return false;
		}

		if(persentase  == ""){
			swal('Peringatan','Persentase tidak boleh kosong.','warning');
			return false;
		}

		total_persentase += parseFloat(persentase);

	    if(total_persentase > 100){
	        swal('Peringatan','Total persentase melebihi 100 %.','warning');
			total_persentase -= parseFloat(persentase);
	        return false;
	    }

	    var markup  = "<tr id='list'>"+
	            "<td width='30%'>"+
	            "<input type='text' class='form-control' value='"+label_target_donasi+"' readonly>"+
	            "<input type='hidden' class='form-control' name='target_donasi_id[]' value='"+target_donasi+"' readonly>"+
	            "</td>"+
	            "<td>"+
	            "<input type='text' class='form-control' name='persentase[]' value='"+persentase+"' id='temp_persentase' readonly>"+
	            "</td>"+
	            "<td>"+
	            "<input type='text' class='form-control' name='jumlah[]' value='"+jumlah+"' id='temp_total' readonly>"+
	            "</td>"+
	            "<td>"+
	            "<button class='btn btn-danger' id='remove' title='Remove Row'>X</button>"+
	            "</td>"+
	            "</tr>";

	    $("#custome-table tbody").append(markup);

		// harga = harga.replace('Rp.','');
		// harga = harga.replace('.','');
		total_donasi = parseFloat(total_donasi) + parseFloat(jumlah);
		// console.log('total_donasi:'+total_donasi);

		// var tes= formatRupiah(total_donasi, 'Rp.');
		$("#total_donasi").val(total_donasi);
	    // $("#total_donasi").val(formatRupiah(total_donasi, 'Rp.'));

	    clear();
	});

	$(document).on('click','#remove',function(){
		var current = $(this).closest("#list");
		var total = current.find("#temp_total").val();
		var persentase = current.find("#temp_persentase").val();
		// current = current.replace('Rp.','');
		// current = current.replace('.','');

	    $(this).parents('tr').remove();
	    total_donasi = parseFloat(total_donasi) - parseFloat(total);
		total_persentase -= parseFloat(persentase);

	    $("#total_donasi").val(total_donasi);
	    // $("#total_donasi").val(formatRupiah(total_donasi, 'Rp.'));
	});
	

});

function updateHarga(){
	var suplierbarang = $('#suplier_barang').text();
	if(suplierbarang == ""){
		alert('Pilih Suplier Barang Terlebih Dahulu');
		$("#jumlah").val("");
		return;
	}

	var harga = suplierbarang.replace(" )", "").split('Rp.')[1].replace(".", "");
	var jumlah = $("#jumlah").val();
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
