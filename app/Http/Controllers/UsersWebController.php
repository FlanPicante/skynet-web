<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;

class UsersWebController extends Controller
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
        if ($role = $request->query('role')) {
            $query['role'] = $role;
        }
        if (!is_null($request->query('active'))) {
            $query['active'] = $request->query('active');
        }

        $res = $api->get('users', ['query' => $query]);
        $body = json_decode($res->getBody()->getContents(), true);

        $users = $body['data'] ?? $body;
        $meta = $body['meta'] ?? null;

        return view('users.index', compact('users', 'meta'));
    }

    public function create(Request $request)
    {
        return view('users.create');
    }

    public function store(Request $request)
    {

        $data = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
            'role_id' => ['required', 'integer'],
            'phone' => ['nullable', 'string'],
        ]);

        $data['active'] = $request->has('active') ? 1 : 0;

        $api = $this->api($request);
        try {
            $api->post('users', [
                'json' => $data,
            ]);
        } catch (ClientException $e) {

            $resp = $e->getResponse();
            $body = json_decode($resp->getBody()->getContents(), true);

            $msg = $body['message'].' '.$e->getMessage() ?? 'Error al crear el usuario.';

           
            return back()
                ->withInput()
                ->withErrors(['general' => $msg]);
        }


        return redirect()
             ->route('users.index')
             ->with('success', 'Usuario creado correctamente.');

    }

    public function edit(Request $request, $id)
    {
        $api = $this->api($request);

        try {
            $res = $api->get("users/{$id}");
        } catch (\Exception $e) {
            return redirect()->route('users.index')->withErrors([
                'general' => 'No se pudo cargar la información del usuario.'
            ]);
        }

        $user = json_decode($res->getBody()->getContents(), true);

        return view('users.edit', compact('user'));
    }

    public function toggleStatus(Request $request, $id)
    {
        $data = $request->validate([
            'active' => ['required', 'boolean'],
        ]);

        $api = $this->api($request);

        try {
            $api->patch("users/{$id}/status", [
                'json' => [
                    'active' => (bool) $data['active'],
                ],
            ]);
        } catch (ClientException $e) {
            $body = json_decode($e->getResponse()->getBody()->getContents(), true);
            return back()->withErrors([
                'general' => $body['message'] ?? 'Error al cambiar el estado del usuario.',
            ]);
        }

        return back()->with('success', 'Estado del usuario actualizado.');
    }

    public function editPassword(Request $request, $id)
    {

        $api = $this->api($request);
        try {
            $res = $api->get("users/{$id}");
            $user = json_decode($res->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return redirect()->route('users.index')->withErrors([
                'general' => 'No se pudo cargar el usuario.',
            ]);
        }

        return view('users.password', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'role_id' => ['required', 'integer'],
            'phone' => ['nullable', 'string', 'max:30'],
            'active' => ['nullable'],
        ]);

        $data['active'] = $request->has('active') ? 1 : 0;

        $api = $this->api($request);

        try {
            $api->put("users/{$id}", [
                'json' => $data,
            ]);
        } catch (ClientException $e) {
            $body = json_decode($e->getResponse()->getBody()->getContents(), true);
            return back()->withInput()->withErrors([
                'general' => $body['message'] ?? 'Error al actualizar el usuario.'
            ]);
        }

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }


    public function updatePassword(Request $request, $id)
    {
        $data = $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'string', 'min:6', 'max:72'],
        ]);

        $api = $this->api($request);

        try {
            $api->patch("users/{$id}/password", [
                'json' => [
                    'current_password' => $data['current_password'],
                    'new_password' => $data['new_password'],
                ],
            ]);
        } catch (ClientException $e) {
            $body = json_decode($e->getResponse()->getBody()->getContents(), true);
            $msg = $body['message'] ?? 'Error al cambiar la contraseña.';

            return back()->withInput()->withErrors([
                'general' => $msg,
            ]);
        }

        return redirect()->route('users.index')->with('success', 'Contraseña actualizada correctamente.');
    }


    public function editTechnicians(Request $request, $id)
    {
        $api = $this->api($request);


        try {
            $resSup = $api->get("users/{$id}");
            $supervisor = json_decode($resSup->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return redirect()->route('users.index')->withErrors([
                'general' => 'No se pudo cargar el supervisor.',
            ]);
        }


        try {
            $resTechs = $api->get('users', [
                'query' => [
                    'role' => 'Tecnico',
                    'active' => 1,
                ],
            ]);
            $bodyTechs = json_decode($resTechs->getBody()->getContents(), true);
            $technicians = $bodyTechs['data'] ?? $bodyTechs;
        } catch (\Exception $e) {
            $technicians = [];
        }


        try {
            $resAssigned = $api->get("supervisors/{$id}/technicians");
            $assignedArr = json_decode($resAssigned->getBody()->getContents(), true);
            $assignedIds = array_column($assignedArr, 'id');
        } catch (\Exception $e) {
            $assignedIds = [];
        }

        return view('users.assign_technicians', compact('supervisor', 'technicians', 'assignedIds'));
    }

    public function updateTechnicians(Request $request, $id)
    {
        $data = $request->validate([
            'technician_ids' => ['array'],
            'technician_ids.*' => ['integer'],
        ]);

        $ids = $data['technician_ids'] ?? [];

        $api = $this->api($request);

        try {
            $api->post("supervisors/{$id}/technicians", [
                'json' => [
                    'technician_ids' => $ids,
                ],
            ]);
        } catch (ClientException $e) {
            $body = json_decode($e->getResponse()->getBody()->getContents(), true);
            return back()->withInput()->withErrors([
                'general' => $body['message'] ?? 'Error al asignar técnicos.',
            ]);
        }

        return redirect()->route('users.index')->with('success', 'Técnicos asignados correctamente.');
    }


}
