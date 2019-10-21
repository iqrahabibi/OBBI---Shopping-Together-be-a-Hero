<div class="form-group {!! $errors->has('detail_user_id') ? 'has-error' : '' !!}">
	{!! Form::label('detail_user_id', 'User', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::select('detail_user_id',[''=>'']+App\Model\DetailUser::list(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih User', 'id'=>'detail_user_id', 'onchange'=>"updateUser()"]) !!}
		{!! $errors->first('detail_user_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('target_donasi_id') ? 'has-error' : '' !!}">
	{!! Form::label('target_donasi_id', 'Target Donasi', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::select('target_donasi_id',[''=>'']+App\Model\TargetDonasi::list(), null,['class'=>'js-selectize', 'placeholder'=>'Pilih Target Donasi']) !!}
		{!! $errors->first('target_donasi_id', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-group {!! $errors->has('jumlah') ? 'has-error' : '' !!}">
	{!! Form::label('jumlah', 'Jumlah Saldo', ['class'=>'col-md-2 control-label']) !!}
	<div class="col-md-10">
		{!! Form::number('jumlah', null, ['class'=>'form-control', 'id'=>'jumlah']) !!}
		{!! $errors->first('jumlah', '<p class="help-block">:message</p>') !!}
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
            <th width='60%'>
				Target Donasi
            </th>
            <th width='30%'>
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
            <td colspan="1"><b>Saldo User</b></td>
            <td colspan="2"><input type="text" class="form-control" name="saldo_user" id="saldo_user" readonly style="text-align:right;font-weight:bold"></td>
        </tr>
        <tr>
            <td colspan="1"><b>Total Donasi</b></td>
            <td colspan="2"><input type="text" class="form-control" name="total_donasi" id="total_donasi" readonly style="text-align:right;font-weight:bold"></td>
        </tr>
    </tfoot>
</table>

@section('scripts')
<script>
var total_donasi   = 0;
var selected_user = null;

$(document).ready(function(){

	function clear(){
		$("#jumlah").val('');
	}

	$(document).on('click','#add',function(){
	    var detail_user_id	= $("#detail_user_id").val();
	    var label_target_donasi	= $("#target_donasi_id").text();
	    var target_donasi	= $("#target_donasi_id").val();
		var jumlah = $("#jumlah").val();

		if(detail_user_id  == ""){
			swal('Peringatan','User tidak boleh kosong.','warning');
			return false;
		}

		if(target_donasi  == ""){
			swal('Peringatan','Target Donasi tidak boleh kosong.','warning');
			return false;
		}

		if(jumlah  == ""){
			swal('Peringatan','Jumlah tidak boleh kosong.','warning');
			return false;
		}

	    var markup  = "<tr id='list'>"+
	            "<td>"+
	            "<input type='text' class='form-control' value='"+label_target_donasi+"' readonly>"+
	            "<input type='hidden' class='form-control' name='target_donasi_id[]' value='"+target_donasi+"' readonly>"+
	            "</td>"+
	            "<td>"+
	            "<input type='text' class='form-control' name='jumlah[]' value='"+jumlah+"' id='temp_total' readonly>"+
	            "</td>"+
	            "<td>"+
	            "<button class='btn btn-danger' id='remove' title='Remove Row'>X</button>"+
	            "</td>"+
	            "</tr>";

	    $("#custome-table tbody").append(markup);

		total_donasi = parseFloat(total_donasi) + parseFloat(jumlah);

	    $("#total_donasi").val(formatRupiah(total_donasi, 'Rp.'));

	    clear();
	});

	$(document).on('click','#remove',function(){
		var current = $(this).closest("#list");
		var total = current.find("#temp_total").val();
		total = total.replace('Rp.','');
		total = total.replace('.','').replace('.','').replace('.','').replace('.','').replace('.','');

	    $(this).parents('tr').remove();
	    total_donasi = parseFloat(total_donasi) - parseFloat(total);

	    $("#total_donasi").val(formatRupiah(total_donasi, 'Rp.'));
	});
	

});

function updateUser(){
	var detail_user_id = $('#detail_user_id').text();
	if(detail_user_id == ""){
		alert('Pilih User Terlebih Dahulu');
		$("#saldo_user").val("");
		return;
	}

	total_donasi   = 0;
	selected_user = null;

	detail_user_id = detail_user_id.split('Rp.')[1];
	detail_user_id = detail_user_id.replace(".", "").replace(".", "").replace(".", "").replace(".", "").replace(".", "");
	
	var saldo = detail_user_id.split(' ')[0];

	$("#saldo_user").val(formatRupiah(saldo, 'Rp.'));
	$("#total_donasi").val(formatRupiah(total_donasi, 'Rp.'));
	$("#custome-table tbody").empty();
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
