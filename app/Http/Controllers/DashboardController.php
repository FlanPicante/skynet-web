<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected function api(Request $request): Client
    {
        $base  = rtrim(config('services.api.base_url'), '/') . '/';
        $token = $request->session()->get('api_token');

        return new Client([
            'base_uri' => $base,
            'timeout'  => 10,
            'headers'  => [
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);
    }

   public function index(Request $request)
    {
        $user   = session('user');
        $api    = $this->api($request);
        $role = $user['role'] ?? null; 
        //TECNICO
        if ($role == 'Tecnico') {
            try {
                $res = $api->get('technicians/me/visits-today');
                $visits = json_decode($res->getBody()->getContents(), true);
            } catch (\Exception $e) {
                \Log::error('Error cargando visitas de hoy para técnico: '.$e->getMessage());
                $visits = [];
            }

           return view('dashboard.tech', compact('visits'));
        }

        //DASH ADMIN Y SUPER
        try {
            $resSummary = $api->get('metrics/summary');
            $summary    = json_decode($resSummary->getBody()->getContents(), true);

            $resVisits = $api->get('visits', [
                'query' => ['limit' => 5]
            ]);
            $bodyVisits = json_decode($resVisits->getBody()->getContents(), true);
            $lastVisits = $bodyVisits['data'] ?? $bodyVisits;

        } catch (\Exception $e) {
            \Log::error('Error cargando métricas para dashboard: '.$e->getMessage());
            $summary   = [];
            $lastVisits = [];
        }

        return view('dashboard.admin', compact('summary','lastVisits'));
    }
}
