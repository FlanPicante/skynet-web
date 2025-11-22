@extends('layouts.app')

@section('content')
    <h1 class="mb-4">Editar visita</h1>

    @if($errors->has('general'))
        <div class="alert alert-danger">{{ $errors->first('general') }}</div>
    @endif

    <form method="POST" action="{{ route('visits.update', $visit['id']) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Cliente</label>
            <input type="text" name="client" class="form-control" id="client" value="{{ $visit['client']['name'] }}" disabled>
        </div>

        <div class="mb-3">
            <label class="form-label">Técnico</label>
            <select name="technician_id" id="technician_id" class="form-control" required>
                <option value="">-- Seleccione --</option>
                @foreach($users as $u)
                    <option value="{{ $u['id'] }}" {{ old('technician_id', $visit['technician_id']) == $u['id'] ? 'selected' : '' }}>
                        {{ $u['name'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Fecha y hora programada</label>
            <input type="datetime-local" name="scheduled_at" class="form-control"
                value="{{ old('scheduled_at', \Carbon\Carbon::parse($visit['scheduled_at'])->format('Y-m-d\TH:i')) }}"
                required>
        </div>

        <div class="mb-3">
            <label class="form-label">Estado</label>
            @php
                
                $currentStatus = old('status', $visit['status'] ?? '1');
            @endphp
            <select name="status" class="form-select">
            
                @foreach($status as $st)
                    <option value="{{ $st['id'] }}" {{ $currentStatus === $st['id'] ? 'selected' : ''  }}>
                        {{ $st['name'] }}
                    </option>
                @endforeach
            </select>
        </div>

        
        <div class="mb-3">
            <label class="form-label">Notas</label>
            <textarea name="notes" class="form-control" rows="2">{{ old('notes', $visit['notes'] ?? '') }}</textarea>
        </div>
        <div id="map" class="mb-4 border rounded" style="height: 300px;"></div>

        <div class="row mb-3">
            <div class="col">
                <label class="form-label">Latitud</label>
                <input id="lat" name="lat" class="form-control" value="{{ old('lat', $visit['client']['lat'] ?? '') }}" disabled>
            </div>
            <div class="col">
                <label class="form-label">Longitud</label>
                <input id="lng" name="lng" class="form-control" value="{{ old('lng', $visit['client']['lng'] ?? '') }}" disabled>
            </div>
        </div>

        

        <button class="btn btn-primary">Actualizar</button>
        <a href="{{ route('visits.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
@endsection

@push('scripts')
    <script>
        const latInput = document.getElementById('lat');
        const lngInput = document.getElementById('lng');

        const startLat = parseFloat(latInput.value);
        const startLng = parseFloat(lngInput.value);

        const map = L.map('map').setView([startLat, startLng], 16);

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

    </script>
@endpush