<div class="form-group {!! $errors->has('judul') ? 'has-error' : '' !!}">
    {!! Form::label('judul', 'Judul') !!}
	{!! Form::text('judul', null, ['class'=>'form-control']) !!}
	{!! $errors->first('judul', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {!! $errors->has('om_barang_kategori_id') ? 'has-error' : '' !!}">
    {!! Form::label('om_barang_kategori_id', 'Kategori Promo') !!}
    @if(empty($data))
        {!! Form::select('om_barang_kategori_id',[''=>'']+App\Model\OMerchantbarangPromosiKategori::list(), null, ['class'=>'js-selectize', 'placeholder'=>'Pilih Kategori','disabled'=>isset($data->om_barang_kategori_id) ? 'disabled' : null]) !!}
    @else
        {!! Form::text('nama_kategori',$data->om_barang_promosi_kategori->nama_kategori.' - '.$data->om_barang_promosi_kategori->deskripsi,['class'=>'form-control','readonly']) !!}
        {!! Form::hidden('om_barang_kategori_id',$data->om_barang_kategori_id,['class' => 'form-control']) !!}
    @endif
	    {!! $errors->first('om_barang_kategori_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group">
    <h2>Promo Rule</h2>
    <hr>
</div>
<div class="form-group {!! $errors->has('min_total_harga_pesanan') ? 'has-error' : '' !!}">
    {!! Form::label('min_total_harga_pesanan', 'Min. Total Harga Penjualan') !!}
	{!! Form::text('min_total_harga_pesanan', null, ['class'=>'form-control']) !!}
	{!! $errors->first('min_total_harga_pesanan', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group row ">
    <div class="col-md-4 {!! $errors->has('jumlah_diskon') ? 'has-error' : '' !!}">
        {!! Form::label('jumlah_diskon', 'Jumlah Diskon') !!}
        {!! Form::text('jumlah_diskon', null, ['class'=>'form-control']) !!}
        {!! $errors->first('jumlah_diskon', '<p class="help-block">:message</p>') !!}
    </div>
    <div class="col-md-4 {!! $errors->has('diskon') ? 'has-error' : '' !!}">
        {!! Form::label('diskon', 'Diskon (%)') !!}
        {!! Form::text('diskon', null, ['class'=>'form-control']) !!}
        {!! $errors->first('diskon', '<p class="help-block">:message</p>') !!}
    </div>
    <div class="col-md-4 {!! $errors->has('max_jumlah_diskon') ? 'has-error' : '' !!}">
        {!! Form::label('max_jumlah_diskon', 'Max. Jumlah Diskon') !!}
        {!! Form::text('max_jumlah_diskon', null, ['class'=>'form-control']) !!}
        {!! $errors->first('max_jumlah_diskon', '<p class="help-block">:message</p>') !!}
    </div>
</div>
<div class="form-group {!! $errors->has('kelipatan') ? 'has-error' : '' !!}">
    {!! Form::label('kelipatan', 'Berlaku Kelipatan') !!}
	{!! Form::checkbox('kelipatan',1) !!}
	{!! $errors->first('kelipatan', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group">
    <h2>Masa Berlaku Promo</h2>
    <hr>
</div>
<div class="form-group row">
    <div class="col-md-6 {!! $errors->has('tanggal_aktif') ? 'has-error' : '' !!}">
        {!! Form::label('tanggal_aktif', 'Tanggal Aktif') !!}
        {!! Form::date('tanggal_aktif', null, ['class'=>'form-control']) !!}
        {!! $errors->first('tanggal_aktif', '<p class="help-block">:message</p>') !!}
    </div>
    <div class="col-md-6 {!! $errors->has('tanggal_berahtir') ? 'has-error' : '' !!}">
        {!! Form::label('tanggal_berahtir', 'Tanggal Berakhir') !!}
        {!! Form::date('tanggal_berakhir', null, ['class'=>'form-control']) !!}
        {!! $errors->first('tanggal_berakhir', '<p class="help-block">:message</p>') !!}
    </div>
</div>
<div class="form-group row">
    <div class="col-md-6 {!! $errors->has('jam_mulai') ? 'has-error' : '' !!}">
        {!! Form::label('jam_mulai', 'Jam Mulai') !!}
        {!! Form::text('jam_mulai', null, ['class'=>'form-control']) !!}
        {!! $errors->first('jam_mulai', '<p class="help-block">:message</p>') !!}
    </div>
    <div class="col-md-6 {!! $errors->has('jam_akhir') ? 'has-error' : '' !!}">
        {!! Form::label('jam_akhir', 'Jam Berakhir') !!}
        {!! Form::text('jam_akhir', null, ['class'=>'form-control']) !!}
        {!! $errors->first('jam_akhir', '<p class="help-block">:message</p>') !!}
    </div>
</div>
{{-- <div class="form-group row col-md-12" id="centang">
    {!! Form::label('hari', 'Berlaku untuk hari: ') !!}
    
            {!! Form::checkbox('hari','All',['id' => 'nachiro']) !!}
            {!! Form::label('hari', 'Semua Hari') !!}
        
            {!! Form::checkbox('hari[]','senin',['class' => 'checkbox']) !!}
            {!! Form::label('hari', 'Senin') !!}
        
            {!! Form::checkbox('hari[]','selasa',['class' => 'checkbox']) !!}
            {!! Form::label('hari', 'Selasa') !!}
        
            {!! Form::checkbox('hari[]','rabu',['class' => 'checkbox']) !!}
            {!! Form::label('hari', 'Rabu') !!}
        
            {!! Form::checkbox('hari[]','kamis',['class' => 'checkbox']) !!}
            {!! Form::label('hari', 'Kamis') !!}
        
            {!! Form::checkbox('hari[]','jumat',['class' => 'checkbox']) !!}
            {!! Form::label('hari', 'Jumat') !!}
       
            {!! Form::checkbox('hari[]','sabtu',['class' => 'checkbox']) !!}
            {!! Form::label('hari', 'Sabtu') !!}
        
            {!! Form::checkbox('hari[]','minggu',['class' => 'checkbox']) !!}
            {!! Form::label('hari', 'Minggu') !!}
           
	{!! $errors->first('kelipatan', '<p class="help-block">:message</p>') !!}
</div> --}}