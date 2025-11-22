@extends('layouts.app')

@section('content')
<h1 class="mb-4">Nuevo cliente</h1>

<form method="POST" action="{{ route('clients.store') }}">
    @csrf

    <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input name="name" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Correo</label>
        <input name="email" type="email" class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">Teléfono</label>
        <input name="phone" class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">Dirección</label>
        <textarea name="address" class="form-control" rows="2"></textarea>
    </div>

    <div class="row mb-3">
        <div class="col">
            <label class="form-label">Latitud</label>
            <input id="lat" name="lat" class="form-control">
        </div>
        <div class="col">
            <label class="form-label">Longitud</label>
            <input id="lng" name="lng" class="form-control">
        </div>
    </div>

    {{-- Mapa --}}
    <div id="map" class="mb-4 border rounded" style="height: 300px;"></div>

    <button class="btn btn-primary">Guardar</button>
</form>
@endsection

@push('scripts')
<script>
    const latInput = document.getElementById('lat');
    const lngInput = document.getElementById('lng');


    const startLat = parseFloat(latInput.value) || 14.6349;
    const startLng = parseFloat(lngInput.value) || -90.5069;

    const map = L.map('map').setView([startLat, startLng], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: ''
    }).addTo(map);

    const marker = L.marker([startLat, startLng], { draggable: true }).addTo(map);

    
    marker.on('dragend', function () {
        const pos = marker.getLatLng();
        latInput.value = pos.lat.toFixed(6);
        lngInput.value = pos.lng.toFixed(6);
    });


    map.on('click', function (e) {
        marker.setLatLng(e.latlng);
        latInput.value = e.latlng.lat.toFixed(6);
        lngInput.value = e.latlng.lng.toFixed(6);
    });
</script>
@endpush
