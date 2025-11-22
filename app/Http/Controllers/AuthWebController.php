<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class AuthWebController extends Controller
{
    protected function api(): Client
    {
        $base = rtrim(config('services.api.base_url'), '/') . '/';

        return new Client([
            'base_uri' => $base,
            'timeout'  => 10,
        ]);
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        try {
            $res = $this->api()->post('auth/login', [
                'json'    => $data,
                'headers' => ['Accept' => 'application/json'],
            ]);

            $body  = json_decode($res->getBody()->getContents(), true);
            $token = $body['token'] ?? null;

            if (!$token) {
                return back()->withErrors(['email' => 'Respuesta inválida del API.']);
            }

 
            $request->session()->put('api_token', $token);


            try {
                $meRes = $this->api()->get('auth/permisos', [
                    'headers' => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . $token,
                    ],
                ]);
                $me = json_decode($meRes->getBody()->getContents(), true);
                $request->session()->put('user', $me);
            } catch (\Exception $e) {
      
            }

            return redirect()->route('dashboard');
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return back()->withErrors(['email' => 'Credenciales inválidas.']);
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'API no disponible.']);
        }
    }

    public function logout(Request $request)
    {
        $token = $request->session()->get('api_token');

        if ($token) {
            try {
                $this->api()->post('auth/logout', [
                    'headers' => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . $token,
                    ],
                ]);
            } catch (\Exception $e) {
      
            }
        }

        $request->session()->flush();

        return redirect()->route('login');
    }
}
