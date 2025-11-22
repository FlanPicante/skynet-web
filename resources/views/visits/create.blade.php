@extends('layouts.app')

@section('content')
<h1 class="mb-4">Nueva visita</h1>

<form method="POST" action="{{ route('visits.store') }}">
    @csrf

    <div class="mb-3">
        <label class="form-label">Cliente</label>
        <select name="client_id" id="client-select" class="form-control" required>
            <option value="">-- Seleccione --</option>
            @foreach($clients as $c)
                <option value="{{ $c['id'] }}"
                    data-lat="{{ $c['lat'] }}"
                    data-lng="{{ $c['lng'] }}"
                    data-address="{{ $c['address'] }}"
                >
                    {{ $c['name'] }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Técnico</label>
        <select name="technician_id" class="form-control" required>
            <option value="">-- Seleccione --</option>
            @foreach($users as $u)
                <option value="{{ $u['id'] }}">{{ $u['name'] }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Fecha y hora programada</label>
        <input type="datetime-local" name="scheduled_at" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Direccion</label>
        <input name="address" id="address" class="form-control" disabled>
    </div>
    

    <div id="map" class="mb-4 border rounded" style="height: 300px;"></div>
    <div class="row mb-3">
        <div class="col">
            <label class="form-label">Latitud</label>
            <input name="lat" id="lat"class="form-control" disabled>
        </div>
        <div class="col">
            <label class="form-label">Longitud</label>
            <input name="lng" id="lng" class="form-control" disabled>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Notas</label>
        <textarea name="notes" class="form-control" rows="2"></textarea>
    </div>

    <button class="btn btn-primary">Guardar</button>
</form>
@endsection


@push('scripts')
<script>
    const latInput = document.getElementById('lat');
    const lngInput = document.getElementById('lng');

    const addressInput = document.getElementById('address');


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



    const clientSelect = document.getElementById('client-select');
    clientSelect.addEventListener('change', function () {
    const option = clientSelect.options[clientSelect.selectedIndex];

    const clientLat = parseFloat(option.dataset.lat);
    const clientLng = parseFloat(option.dataset.lng);
    const clientAddress = option.dataset.address;

    if (!isNaN(clientLat) && !isNaN(clientLng)) {
        
        latInput.value = clientLat;
        lngInput.value = clientLng;
        addressInput.value = clientAddress;
        
        marker.setLatLng([clientLat, clientLng]);
        map.setView([clientLat, clientLng], 14);

        console.log("Cliente seleccionado:", option.dataset);
        console.log("Coordenadas:", clientLat, clientLng);
    }
});
</script>
@endpush
