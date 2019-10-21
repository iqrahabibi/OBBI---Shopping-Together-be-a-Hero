@extends('layouts.app')

@section('styles')

@endsection

@section('content')
<div class="page-head">
	<div class="page-title">
		<h1>Kasir</h1>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="portlet light">
			<div class="portlet-body">
                <div class="form-group">
                    <table class="table table-bordered">
                        <tr>
                            <th>{!! Form::label('sku','SKU',['class' => 'control-label']) !!}</th>
                            <th>{!! Form::label('barang','Nama Barang',['class' => 'control-label']) !!}</th>
                            <th>{!! Form::label('varian','Varian',['class' => 'control-label']) !!}</th>
                            <th>{!! Form::label('qty','Quantity',['class' => 'control-label']) !!}</th>
                            <th>{!! Form::label('harga','Harga Satuan (Rp. )',['class' => 'control-label']) !!}</th>
                            <th>{!! Form::label('total_harga','Total Harga (Rp. )',['class' => 'control-label']) !!}</th>
                            <th></th>
                        </tr>
                        <tr>
                            <td>
                                {!! Form::text('sku',null,['class' => 'form-control sku']) !!}
                            </td>
                            <td width='20%'>
                                {!! Form::select('barang',['0'=>'--- Pilih Barang ---'],null,['class' => 'js-selectize barangs','id' => 'barangs']) !!}
                            </td>
                            <td>
                                {!! Form::text('varian',null,['class' => 'form-control varian','readonly']) !!}
                            </td>
                            <td>
                                {!! Form::number('qty',null,['class' => 'form-control qty']) !!}
                            </td>
                            <td>
                                {!! Form::text('harga_satuan',null,['class' => 'form-control harga_satuan','readonly']) !!}
                                {!! Form::hidden('belanja_id',null,['class' => 'form-control belanja_id','readonly']) !!}
                            </td>
                            <td>
                                {!! Form::text('total_harga',null,['class' => 'form-control total_harga','readonly']) !!}
                            </td>
                            <td>
                                <button class="btn btn-info tambah" id="tambah">Add</button>
                            </td>
                        </tr>
                    </table>
                </div>
                {!! Form::open(['route' => 'kasir.store'])!!}
                    @include('omerchantadmin.offline.kasir._form')
                    {!! Form::submit(isset($data) ? 'Update' : 'Save', ['class'=>'btn btn-primary']) !!}
                {!! Form::close() !!}
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/scannerdetection/1.2.0/jquery.scannerdetection.js"></script>

<script>
    $(document).ready(function(){

        $( ".sku" ).keyup(function() {
            search($(this).val());
        });

        $(document).scannerDetection({
            timeBeforeScanTest: 200, // wait for the next character for upto 200ms
            startChar: [120], // Prefix character for the cabled scanner (OPL6845R)
            endChar: [13], // be sure the scan is complete if key 13 (enter) is detected
            avgTimeByChar: 40, // it's not a barcode if a character takes longer than 40ms
            onComplete: function(barcode, qty){ 
                console.log(barcode);
                console.log(qty);

                $('.sku').val(barcode);

                search(barcode);
                
            } // main callback function    
        });

        function search(sku){
            $.ajax({
                type: 'POST',
                url : '/kasir/barang',
                data    : {id:sku},
                headers : {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success : function(response){
                    console.log(response);
                    var selectize_data = $(".barangs")[0].selectize;
                        selectize_data.clearOptions();
                    var data = jQuery.parseJSON(response);
                    for (var i = 0; i < data.hasil.length; i++) {
                        selectize_data.addOption({
                            text:data.hasil[i].nama_barang,
                            value:data.hasil[i].id
                        });
                        selectize_data.refreshOptions() ;
                    }
                }
            });
        }

        var total_belanja   = 0;

        function clear(){
            $("#barang").val('0');
            $(".qty").val('');
            $(".varian").val('');
            $(".harga_satuan").val('');
            $(".total_harga").val('');
            $(".belanja_id").val('');
            $(".sku").val('');
        }

        $('.barangs').change(function(){
            var barang  = $('.barangs').val();
            $('.qty').val(1);
            $.ajax({
                type    :'POST',
                url     : '/kasir/harga_satuan',
                data    : {id:barang},
                headers : {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success : function(response){

                    $('.harga_satuan').val(response['harga_jual']);
                    $('.varian').val(response['varian']);
                    $('.belanja_id').val(response['belanja_id']);
                    $('.total_harga').val(response['harga_jual']);
                }
            })
        });

        $(document).on('keyup change','.qty',function(){
            var qty             = $(this).val();
            var harga_satuan    = $(".harga_satuan").val();

            if(qty == ""){
                $(this).val(1);
                $('.total_harga').val(""+harga_satuan);
            }else{
                var total   = parseInt(harga_satuan)*parseInt(qty);
            
                $('.total_harga').val(total);
            }
            
        });

        $(document).on('click','.tambah',function(){
            var barang          = $(".barangs").text();
            var barang_id       = $('.barangs').val();
            var qty             = $(".qty").val();
            var varian          = $('.varian').val();
            var harga_satuan    = $(".harga_satuan").val();
            var total_harga     = $(".total_harga").val();
            var belanja_id      = $(".belanja_id").val();

            if(barang  == ""){
                swal('Peringatan','Barang tidak boleh kosong.','warning');

                return false;
            }

            if(qty.length < 1){
                swal('Peringatan','Quantity tidak boleh kosong.','warning');

                return false;
            }

            var markup  = "<tr id='list'>"+
                    "<td width='30%'>"+
                    "<input type='text' class='form-control' value='"+barang+"' readonly>"+
                    "<input type='hidden' class='form-control' name='barang[]' value='"+barang_id+"' readonly>"+
                    "<input type='hidden' class='form-control' name='belanja_id[]' value='"+belanja_id+"' readonly>"+
                    "</td>"+
                    "<td>"+
                    "<input type='text' class='form-control' name='varian[]' value='"+varian+"' readonly>"+
                    "</td>"+
                    "<td>"+
                    "<input type='text' class='form-control' name='qty[]' value='"+qty+"' readonly>"+
                    "</td>"+
                    "<td>"+
                    "<input type='text' class='form-control' name='harga_satuan[]' value='"+harga_satuan+"' readonly>"+
                    "</td>"+
                    "<td>"+
                    "<input type='text' class='form-control' name='total_harga[]' value='"+total_harga+"' id='total' readonly>"+
                    "</td>"+
                    "<td>"+
                    "<button class='btn btn-danger' id='remove' title='Remove Row'>X</button>"+
                    "</td>"+
                    "</tr>";

            $("#custome-table tbody").append(markup);

             total_belanja = parseInt(total_belanja)+parseInt(total_harga);

            $("#total_belanja").val(total_belanja);

            clear();
        });

        $(document).on('click','#remove',function(){
            var current = $(this).closest("#list").find("#total").val();
            $(this).parents('tr').remove();

            total_belanja = parseInt(total_belanja)-parseInt(current);

            $("#total_belanja").val(total_belanja);
        });
    });

</script>
@endsection