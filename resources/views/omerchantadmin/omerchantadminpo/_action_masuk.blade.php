@if($model->status == 'Closed')
    <a href="{{ $cetak_url }}" class="btn btn-xs btn-primary">Cetak</a> | 
@elseif($model->status == 'Checked')
    <a href="{{ $pomasuk_url }}" class="btn btn-xs btn-primary">Proses</a> | 
@endif

<a href="{{ $retur }}" class="btn btn-xs btn-success" target="_blank">Retur</a> | 
<a href="{{ $detail_url }}" class="btn btn-xs btn-info" target="_blank">Detail</a>