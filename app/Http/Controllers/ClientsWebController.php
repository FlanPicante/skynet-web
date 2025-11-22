<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class ClientsWebController extends Controller
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

        $res = $api->get('clients', [
            'query' => $query,
        ]);
        $body = json_decode($res->getBody()->getContents(), true);

        $clients = $body['data'] ?? $body;
        $meta = $body['meta'] ?? null;

        return view('clients.index', compact('clients', 'meta'));

    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
            'address' => ['nullable', 'string'],
        ]);

        $api = $this->api($request);

        $api->post('clients', [
            'json' => $data,
        ]);

        return redirect()->route('clients.index')->with('success', 'Cliente creado correctamente.');
    }

    public function edit(Request $request, $id)
    {
        $api = $this->api($request);

        try {
            $res = $api->get("clients/{$id}");
            $client = json_decode($res->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return redirect()->route('clients.index')->withErrors([
                'general' => 'No se pudo cargar la información del cliente.',
            ]);
        }

        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'email' => ['nullable', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
        ]);

        $data['active'] = $request->has('active') ? 1 : 0;

        $api = $this->api($request);

        try {
            $api->put("clients/{$id}", [
                'json' => $data,
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $body = json_decode($e->getResponse()->getBody()->getContents(), true);

            return back()
                ->withInput()
                ->withErrors([
                    'general' => $body['message'] ?? 'Error al actualizar el cliente.',
                ]);
        }

        return redirect()
            ->route('clients.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

}
