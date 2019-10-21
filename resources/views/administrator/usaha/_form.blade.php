<div class="form-group {!! $errors->has('kelurahan_id') ? 'has-error' : '' !!}">
    {!! Form::label('kelurahan_id', 'Nama Kelurahan', ['class'=>'col-md-2 control-label']) !!}
    <div class="col-md-10">
        @if(isset($data))
            {!! Form::text('kelurahan_id', $data->kelurahan->nama_kelurahan, ['class'=>'form-control', 'disabled'=>'disabled']) !!}
        @else
            {!! Form::select('kelurahan_id',[''=>''] ,null, ['class'=>'js-selectize','placeholder' => 'Pilih Kelurahan','disabled'=>isset($data->kecamatan_id) ? 'disabled' : null]) !!}
        @endif
        {!! $errors->first('kelurahan_id', '<p class="help-block">:message</p>') !!}
    </div>
</div>
<div class="form-group {!! $errors->has('nama_usaha') ? 'has-error' : '' !!}">
    {!! Form::label('nama_usaha','Nama Usaha', ['class'=>'col-md-2 control-label']) !!}
    <div class="col-md-10">
        {!! Form::text('nama_usaha',null,['class' => 'form-control']) !!}
        {!! $errors->first('nama_usaha','<p class="help-block">:message</p>') !!}
    </div>
</div>
<div class="form-group {!! $errors->has('deskripsi_usaha') ? 'has-error' : '' !!}">
    {!! Form::label('deskripsi_usaha','Deskripsi Usaha', ['class'=>'col-md-2 control-label']) !!}
    <div class="col-md-10">
        {!! Form::text('deskripsi_usaha',null,['class' => 'form-control']) !!}
        {!! $errors->first('deskripsi_usaha','<p class="help-block">:message</p>') !!}
    </div>
</div>
<div class="form-group {!! $errors->has('total_modal') ? 'has-error' : '' !!}">
    {!! Form::label('total_modal','Total Modal', ['class'=>'col-md-2 control-label']) !!}
    <div class="col-md-10">
        {!! Form::text('total_modal',null,['class' => 'form-control']) !!}
        {!! $errors->first('total_modal','<p class="help-block">:message</p>') !!}
    </div>
</div>
<div class="form-group {!! $errors->has('alamat') ? 'has-error' : '' !!}">
    {!! Form::label('alamat','Alamat', ['class'=>'col-md-2 control-label']) !!}
    <div class="col-md-10">
        {!! Form::text('alamat',null,['class' => 'form-control', 'id'=>'cari']) !!}
        {!! $errors->first('alamat','<p class="help-block">:message</p>') !!}
    </div>
</div>

<div class="form-group {!! $errors->has('latitude') ? 'has-error' : '' !!}">
    {!! Form::label('latitude','Koordinat Latitude', ['class'=>'col-md-2 control-label']) !!}
    <div class="col-md-10">
        {!! Form::text('latitude',null,['class' => 'form-control', 'id'=>'latitude', 'readonly']) !!}
        {!! $errors->first('latitude','<p class="help-block">:message</p>') !!}
    </div>
</div>
<div class="form-group {!! $errors->has('longitude') ? 'has-error' : '' !!}">
    {!! Form::label('longitude','Koordinat Longitude', ['class'=>'col-md-2 control-label']) !!}
    <div class="col-md-10">
        {!! Form::text('longitude',null,['class' => 'form-control', 'id'=>'longitude', 'readonly']) !!}
        {!! $errors->first('longitude','<p class="help-block">:message</p>') !!}
    </div>
</div>
<div class="form-group">
    <div class="col-md-2"></div>
    <div class="col-md-10">
        <div id="map" style="height:400px"></div>
    </div>
</div>

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
});
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAHjGSRX66EgP_ZDLrUEKSqTItbI_pxJYU&libraries=places&callback=initAutocomplete" async defer></script>
<script type="text/javascript">
	$(document).ready(function() {
		$(window).keydown(function(event){
			if (event.keyCode == 13) {
				event.preventDefault();
				return false;
			}
		});
		
	});

	var marker;
	
	function initAutocomplete() {
		<?php
			if(isset($data) && $data->latitude && $data->longitude){
		?>
			var posisi = {lat: <?php echo $data->latitude; ?>, lng: <?php echo $data->longitude; ?>};
			var map = new google.maps.Map(document.getElementById('map'), {
				zoom: 11,
				center: posisi,
				streetViewControl: false,
				mapTypeId: google.maps.MapTypeId.TERRAIN  
				// mapTypeId: 'roadmap'
			});
		<?php
			}else{
		?>
			var map = new google.maps.Map(document.getElementById('map'), {
				zoom: 11,
				center: {lat: -6.17511, lng: 106.8650395},
				streetViewControl: false,
				mapTypeId: google.maps.MapTypeId.TERRAIN  
				// mapTypeId: 'roadmap'
			});
		<?php
			}
		?>

		// Listener
		google.maps.event.addListener(map, 'click', function(event) {
			addMarker(event.latLng, map);
		});

		// Create the search box and link it to the UI element.
		var input = document.getElementById('cari');
		var searchBox = new google.maps.places.SearchBox(input);
		// map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

		// Bias the SearchBox results towards current map's viewport.
		map.addListener('bounds_changed', function() {
		searchBox.setBounds(map.getBounds());
		});

		var markers = [];
		// Listen for the event fired when the user selects a prediction and retrieve
		// more details for that place.
		searchBox.addListener('places_changed', function() {
			var places = searchBox.getPlaces();

			if (places.length == 0) {
				return;
			}

			// Clear out the old markers.
			markers.forEach(function(marker) {
				marker.setMap(null);
			});
			markers = [];

			// For each place, get the icon, name and location.
			var bounds = new google.maps.LatLngBounds();
			places.forEach(function(place) {
				if (!place.geometry) {
					console.log("Returned place contains no geometry");
					return;
				}
				var icon = {
					url: place.icon,
					size: new google.maps.Size(71, 71),
					origin: new google.maps.Point(0, 0),
					anchor: new google.maps.Point(17, 34),
					scaledSize: new google.maps.Size(25, 25)
				};

				if (place.geometry.viewport) {
					// Only geocodes have viewport.
					bounds.union(place.geometry.viewport);
				} else {
					bounds.extend(place.geometry.location);
				}
			});
			map.fitBounds(bounds);
		});
		

		<?php
			if(isset($data) && $data->latitude && $data->longitude){
		?>
			var posisi = {lat: <?php echo $data->latitude; ?>, lng: <?php echo $data->longitude; ?>};
			marker = new google.maps.Marker({
				position: posisi,
				map: map,
				draggable: true,
				animation: google.maps.Animation.DROP,
			});
		<?php
			}
		?>
	}

	function addMarker(location, map) {
		// Add the marker at the clicked location, and add the next-available label
		// from the array of alphabetical characters.
				
		if(marker != null){
			marker.setMap(null);
		}

		console.log('location lat:'+location.lat());
		console.log('location lng:'+location.lng());
	
		marker = new google.maps.Marker({
			position: location,
			map: map,
			draggable: true,
			animation: google.maps.Animation.DROP,
		});
		
		google.maps.event.addListener(marker, 'click', function(event) {
			marker.setMap(null);
		});

		var latitude = document.getElementById("latitude");
		var longitude = document.getElementById("longitude");

		latitude.value = location.lat();
		longitude.value = location.lng();
		// var textBiaya = document.getElementById("biaya_retribusi");
		// var span = document.getElementsByClassName("close")[0];

		// modal = document.getElementById('myModal');
		// modal.style.display = "block";

		// btn.onclick = function() {
		// 	ajaxSimpan(marker,$("#biaya_retribusi").val());
		// }
	}
</script>
@endsection
