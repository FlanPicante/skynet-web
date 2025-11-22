@extends('layouts.app')

@section('content')
<h1 class="mb-4">Editar cliente</h1>

@if($errors->has('general'))
    <div class="alert alert-danger">{{ $errors->first('general') }}</div>
@endif

<form method="POST" action="{{ route('clients.update', $client['id']) }}">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input name="name"
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $client['name'] ?? '') }}"
               required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Correo</label>
        <input type="email"
               name="email"
               class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $client['email'] ?? '') }}">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Teléfono</label>
        <input name="phone"
               class="form-control @error('phone') is-invalid @enderror"
               value="{{ old('phone', $client['phone'] ?? '') }}">
        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Dirección</label>
        <textarea name="address"
                  class="form-control @error('address') is-invalid @enderror"
                  rows="2">{{ old('address', $client['address'] ?? '') }}</textarea>
        @error('address')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="row mb-3">
        <div class="col">
            <label class="form-label">Latitud</label>
            <input id="lat" name="lat"
                   class="form-control @error('lat') is-invalid @enderror"
                   value="{{ old('lat', $client['lat'] ?? '') }}">
            @error('lat')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col">
            <label class="form-label">Longitud</label>
            <input id="lng" name="lng"
                   class="form-control @error('lng') is-invalid @enderror"
                   value="{{ old('lng', $client['lng'] ?? '') }}">
            @error('lng')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="mb-3 form-check">
        <input type="checkbox"
               class="form-check-input"
               id="active"
               name="active"
               {{ old('active', $client['active'] ?? 1) ? 'checked' : '' }}>
        <label class="form-check-label" for="active">Cliente activo</label>
    </div>

    <div id="map" class="mb-4 border rounded" style="height: 300px;"></div>

    <button class="btn btn-primary">Guardar cambios</button>
    <a href="{{ route('clients.index') }}" class="btn btn-secondary">Cancelar</a>
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
        attribution: '© OpenStreetMap contributors'
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
