@extends('layouts.app')

@section('content')
    <h1 class="mb-4">Detalle de visita #{{ $visit['id'] }}
    </h1>

    @if($errors->has('general'))
        <div class="alert alert-danger">{{ $errors->first('general') }}</div>
    @endif
    @php
        $statusId = $visit['status'] ?? $visit['status'] ?? null;
        $disableAll = ($statusId == 5);
        $disableEnRuta = $disableAll || ($statusId == 2);
        $disableMapsWaze = $disableAll || ($statusId == 1);

        $disableCheckIn = ($statusId != 2);
        $disableCheckOut = ($statusId !=3);

        $lat=$visit['client']['lat'];
        $lng=$visit['client']['lng'];

        $mapsUrl =  $lat && $lng
            ? "https://www.google.com/maps/dir/?api=1&destination={$lat},{$lng}"
            : "#";

        $wazeUrl = $lat && $lng
            ? "https://waze.com/ul?ll={$lat},{$lng}&navigate=yes"
            : "#";
    @endphp

    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex gap-2 mb-4">
                <form method="POST" action="{{ route('visits.onroute', $visit['id']) }}">
                    @csrf
                    <button class="btn btn-sm btn-warning" @if($disableEnRuta) disabled @endif>
                        En Ruta
                    </button>
                </form>

                <a href="{{ $disableMapsWaze ? '#' : $mapsUrl }}"
                    class="btn btn-sm btn-primary {{ $disableMapsWaze ? 'disabled' : '' }}" @if($disableMapsWaze)
                    tabindex="-1" aria-disabled="true" @else target="_blank" @endif>
                    Google Maps
                </a>

                
                <a href="{{ $disableMapsWaze ? '#' : $wazeUrl }}"
                    class="btn btn-sm btn-info {{ $disableMapsWaze ? 'disabled' : '' }}" @if($disableMapsWaze) tabindex="-1"
                    aria-disabled="true" @else target="_blank" @endif>
                    Waze
                </a>

                
                <a href="{{ $disableAll ? '#' : route('visits.today') }}"
                    class="btn btn-sm btn-secondary {{ $disableAll ? 'disabled' : '' }}" @if($disableAll) tabindex="-1"
                    aria-disabled="true" @endif>
                    Cancelar
                </a>
            </div>
            <h5 class="card-title mb-3">
                {{ $visit['client']['name'] ?? 'Cliente sin nombre' }}
            </h5>

            <div class="row mb-2">
                <div class="col-md-6">
                    <strong>Técnico:</strong>
                    {{ $visit['user']['name'] ?? '—' }}
                </div>
                <div class="col-md-6">
                    <strong>Supervisor:</strong>
                    {{ $visit['supervisor']['name'] ?? '—' }}
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-6">
                    <strong>Programada para:</strong>
                    @if(!empty($visit['scheduled_at']))
                        {{ \Carbon\Carbon::parse($visit['scheduled_at'])->format('d/m/Y H:i') }}
                    @else
                        —
                    @endif
                </div>
                <div class="col-md-6">
                    <strong>Estado:</strong>
                    @php
                        $status = $visit['statuss'] ?? '1';
                        $badgeClass = match ($status['id']) {
                            '1' => 'bg-secondary',
                            '2' => 'bg-info',
                            '3' => 'bg-info',
                            '4' => 'bg-success',
                            '5' => 'bg-danger',
                            default => 'bg-secondary',
                        };

                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ $status['name'] }}</span>
                </div>
            </div>

            <div class="mb-2">
                <strong>Notas:</strong><br>
                {{ $visit['notes'] ?? '—' }}
            </div>
        </div>
    </div>


    <div class="card mb-3">
        <div class="card-header">
            Ubicación programada
        </div>
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-6">
                    <strong>Direccion</strong>
                    {{ $visit['client']['address'] }}
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-6">
                    <strong>Latitud:</strong> {{ $lat ?? '—' }}
                </div>
                <div class="col-md-6">
                    <strong>Longitud:</strong> {{ $lng ?? '—' }}
                </div>
            </div>

            <div id="visit-map" class="border rounded" style="height: 300px;"></div>
        </div>
    </div>


    <div class="card">
        <div class="card-header">
            Acciones
        </div>
        <div class="card-body">
            <p class="text-muted small">
                Al hacer check-in / check-out se usara tu ubicación actual.
            </p>

            <div class="row g-3">
                <div class="col-md-6">
                    <form method="POST" action="{{ route('visits.checkin', $visit['id']) }}" id="form-checkin">
                        @csrf
                        <input type="hidden" name="lat" id="checkin-lat">
                        <input type="hidden" name="lng" id="checkin-lng">

                        <button type="button" class="btn btn-success w-100" onclick="useCurrentLocation('checkin')" {{$disableCheckIn?'disabled':'' }}>
                            Check-in
                        </button>
                    </form>
                </div>

                <div class="col-md-6">
                    <form method="POST" action="{{ route('visits.checkout', $visit['id']) }}" id="form-checkout">
                        @csrf
                        <input type="hidden" name="lat" id="checkout-lat">
                        <input type="hidden" name="lng" id="checkout-lng">

                        <button type="button" class="btn btn-danger w-100" onclick="useCurrentLocation('checkout')" {{ $disableCheckOut?'disabled':'' }}>
                            Check-out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>

        const lat = {{ $lat ?? 'null' }};
        const lng = {{ $lng ?? 'null' }};

        let startLat = 14.6349;
        let startLng = -90.5069;
        let zoom = 12;

        if (lat !== null && lng !== null) {
            startLat = lat;
            startLng = lng;
            zoom = 14;
        }

        const map = L.map('visit-map').setView([startLat, startLng], zoom);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: ''
        }).addTo(map);

        if (lat !== null && lng !== null) {
            L.marker([lat, lng]).addTo(map);
        }


        function useCurrentLocation(type) {
            if (!navigator.geolocation) {
                alert('Tu navegador no soporta geolocalización. Ingresa la ubicación manualmente si es requerido.');
                return;
            }

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    const latInput = document.getElementById(type + '-lat');
                    const lngInput = document.getElementById(type + '-lng');
                    const form = document.getElementById('form-' + type);

                    latInput.value = lat;
                    lngInput.value = lng;

                    form.submit();
                },
                function (error) {
                    console.error(error);
                    alert('No se pudo obtener tu ubicación. Verifica permisos de GPS / navegador.');
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        }
    </script>
@endpush