<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportsController extends Controller
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

    public function clients(Request $request)
    {
        $api = $this->api($request);

        $res = $api->get('clients');

        $body    = json_decode($res->getBody()->getContents(), true);
        $clients = $body['data'] ?? $body;

       
        if ($request->query('export') === 'pdf') {
            $pdf = Pdf::loadView('reports.clients_pdf', compact('clients'))
                ->setPaper('letter', 'portrait');

            return $pdf->download('reporte_clientes.pdf');
        }

        
        return view('reports.clients', compact('clients'));
    }

    public function visits(Request $request){
        $api= $this->api($request);
        $query = [];

        $search = $request->query('search');
        $status = $request->query('status');
        $from   = $request->query('from');
        $to     = $request->query('to');

         if ($search) {
            $query['search'] = $search;
        }
        if ($status) {
            $query['status'] = $status;
        }
        if ($from) {
            $query['from'] = $from;
        }
        if ($to) {
            $query['to'] = $to;
        }

        try {
            $res = $api->get('visits', [
                'query' => $query,
            ]);

            $body   = json_decode($res->getBody()->getContents(), true);
            $visits = $body['data'] ?? $body;

            $resStatus   = $api->get('status');
            $statusList  = json_decode($resStatus->getBody()->getContents(), true);
        } catch (\Exception $e) {
            \Log::error('Error cargando reporte de visitas: '.$e->getMessage());
            $visits     = [];
            $statusList = [];
        }

         if ($request->query('export') === 'pdf') {
            $pdf = Pdf::loadView('reports.visits_pdf', [
                'visits' => $visits,
                'filters' => [
                    'search' => $search,
                    'status' => $status,
                    'from'   => $from,
                    'to'     => $to,
                ],
            ])->setPaper('letter', 'landscape');

            return $pdf->download('reporte_visitas.pdf');
        }

        return view('reports.visits', compact('visits', 'statusList'));
    }
}
