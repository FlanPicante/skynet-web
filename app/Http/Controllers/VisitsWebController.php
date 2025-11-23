<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;

class VisitsWebController extends Controller
{
    protected function api(Request $request): Client
    {
        $base = rtrim(config('services.api.base_url'), '/') . '/';
        $token = $request->session()->get('api_token');

        return new Client([
            'base_uri' => $base,
            'timeout' => 10,
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);
    }

    public function index(Request $request)
    {
        $api = $this->api($request);

        $query = [];

        if ($search = $request->query('search')) {
            $query['search'] = $search;
        }
        if ($status = $request->query('status')) {
            $query['status'] = $status;
        }
        if ($from = $request->query('from')) {
            $query['from'] = $from;
        }
        if ($to = $request->query('to')) {
            $query['to'] = $to;
        }
        if ($page = $request->query('page')) {
            $query['page'] = $page;
        }

        $res = $api->get('visits', [
            'query' => $query,
        ]);

        $resStatus = $api->get('status');

        $status = json_decode($resStatus->getBody()->getContents(), true);


        $body = json_decode($res->getBody()->getContents(), true);

        $visits = $body['data'] ?? $body;
        $meta = $body['meta'] ?? $body;

        return view('visits.index', compact('visits', 'meta', 'status'));
    }

    public function edit(Request $request, $id)
    {
        $api = $this->api($request);

        $me = session('user');
        $role = $me['role'] ?? '';



        //VISITAS
        try {
            $resVisit = $api->get("visits/{$id}");

            $resStatus = $api->get("status");



            if ($role == 'Supervisor') {
                $usersRes = $api->get('supervisors/' . $me['id'] . '/technicians');
            } else if ($role == 'Admin') {
                $usersRes = $api->get('users', ['query' => ['role' => 'Tecnico']]);
            }


        } catch (\Exception $e) {
            return redirect()->route('users.index')->withErrors([
                'general' => 'No se pudo cargar la información de la visita.'
            ]);
        }



        $visit = json_decode($resVisit->getBody()->getContents(), true);
        $status = json_decode($resStatus->getBody()->getContents(), true);
        $bodyUsers = json_decode($usersRes->getBody()->getContents(), true);

        $users = $bodyUsers['data'] ?? $bodyUsers;

        return view('visits.edit', compact('visit', 'users', 'status'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'technician_id' => ['required', 'integer'],
            'status' => ['required', 'integer'],
            'scheduled_at' => ['required', 'string'],
            'notes' => ['nullable', 'string', 'max:30'],
        ]);


        $api = $this->api($request);

        try {
            $api->put("visits/{$id}", [
                'json' => $data,
            ]);
        } catch (ClientException $e) {
            $body = json_decode($e->getResponse()->getBody()->getContents(), true);
            return back()->withInput()->withErrors([
                'general' => $body['message'] . $request ?? 'Error al actualizar la visita.'
            ]);
        }

        return redirect()->route('visits.index')->with('success', 'Visita actualizado correctamente.');
    }


    public function create(Request $request)
    {

        $api = $this->api($request);

        $clientsRes = $api->get('clients');
        $clientsBody = json_decode($clientsRes->getBody()->getContents(), true);
        $clients = $clientsBody['data'] ?? $clientsBody;

        $me = session('user');
        $role = $me['role'] ?? '';
        if ($role == 'Supervisor') {
            $usersRes = $api->get('supervisors/' . $me['id'] . '/technicians');
        } else if ($role == 'Admin') {
            $usersRes = $api->get('users', ['query' => ['role' => 'Tecnico']]);
        }


        $usersBody = json_decode($usersRes->getBody()->getContents(), true);
        $users = $usersBody['data'] ?? $usersBody;
        return view('visits.create', compact('clients', 'users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => ['required', 'integer'],
            'technician_id' => ['required', 'integer'],
            'scheduled_at' => ['required', 'date'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
            'notes' => ['nullable', 'string'],
        ]);

        $api = $this->api($request);

        $api->post('visits', [
            'json' => $data,
        ]);

        return redirect()->route('visits.index')->with('success', 'Visita creada correctamente.');
    }

    public function today(Request $request)
    {
        $api = $this->api($request);

        try {
            $res = $api->get('technicians/me/visits-today');
            $body = json_decode($res->getBody()->getContents(), true);

            $visits = $body['data'] ?? $body;
        } catch (\Exception $e) {
            return back()->withErrors(['general' => 'No se pudieron cargar tus visitas de hoy.' . $e->getMessage()]);
        }

        return view('visits.today', compact('visits'));
    }

    public function show(Request $request, $id)
    {
        $api = $this->api($request);

        try {
            $res = $api->get("visits/{$id}");
            $visit = json_decode($res->getBody()->getContents(), true);
        } catch (\Exception $e) {
            \Log::error('Error al obtener visita: ' . $e->getMessage());
            return back()->withErrors(['general' => 'No se pudo cargar la visita.']);
        }

        return view('visits.show', compact('visit'));
    }

    public function markOnRoute(Request $request, $id)
    {
        try {
            $api = $this->api($request);

            
            $res = $api->post("visits/{$id}/events", [
                'form_params' => [
                    'event_type' => 'en_ruta',
                    'status' => '2',
                ]
            ]);

            return back()->with('success', 'Visita marcada como EN RUTA');
        } catch (\Exception $e) {
            return back()->withErrors(['general' => 'No se pudo marcar En Ruta.'.$e->getMessage()]);
        }
    }

    public function checkIn(Request $request, $id)
    {
        $api = $this->api($request);

        try {
            $payload = [
                'lat' => $request->input('lat'),
                'lng' => $request->input('lng'),
            ];

            $api->post("visits/{$id}/checkin", [
                'json' => $payload,
            ]);

            return back()->with('success', 'Check-in registrado correctamente.');
        } catch (\Exception $e) {
            \Log::error('Error en check-in: ' . $e->getMessage());
            return back()->withErrors(['general' => 'No se pudo registrar el check-in.'.$e->getMessage()]);
        }
    }

    public function checkOut(Request $request, $id)
    {
        $api = $this->api($request);

        try {
            $payload = [
                'lat' => $request->input('lat'),
                'lng' => $request->input('lng'),
            ];

            $api->post("visits/{$id}/checkout", [
                'json' => $payload,
            ]);

            return back()->with('success', 'Check-out registrado correctamente.');
        } catch (\Exception $e) {
            \Log::error('Error en check-out: ' . $e->getMessage());
            return back()->withErrors(['general' => 'No se pudo registrar el check-out.']);
        }
    }
}
