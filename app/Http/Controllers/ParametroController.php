<?php

namespace App\Http\Controllers;

use App\Models\Parametro;
use Illuminate\Http\Request;
use Artisaninweb\SoapWrapper\SoapWrapper;


class ParametroController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $url;
    private $strCompany;

    public function index()
    {
        $tarifas = Parametro::all();
        return $tarifas;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Parametro  $parametro
     * @return \Illuminate\Http\Response
     */
    public function show(Parametro $parametro)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Parametro  $parametro
     * @return \Illuminate\Http\Response
     */
    public function edit(Parametro $parametro)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Parametro  $parametro
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Parametro $parametro)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Parametro  $parametro
     * @return \Illuminate\Http\Response
     */
    public function destroy(Parametro $parametro)
    {
        //
    }

    public function getParametroFilter($agrupador)
    {
        $record = Parametro::where('agrupador', '=', $agrupador)
            ->where('estado', '=', 'A')
            ->first();
        $this->url = $record->valor;
        $this->strCompany = $record->parametro;
        $this->bunit = $record->bunit;
        $this->email = $record->email;
        return ['url' => $this->url,
                'strCompany' =>$this->strCompany,
                'bunit' => $this->bunit,
                'email' => $this->email] ;
    }



    public function getAgentsList()
    {
        $params = ['getAgentsList' => [ 'strCompanyName' => 'I CHOSE TODAY']
        ];
        $this->getParametroFilter('WSDLDyn');
        try {
            $soapWrapper = new SoapWrapper;
            $soapWrapper->add('getAgentsList', function ($service) {
                $service->wsdl($this->url)
                ->trace(true);
              });
              
              $response = $soapWrapper->call('getAgentsList.getAgentsList', [
                'body' => ['strCompanyName' => $this->strCompany]
              ]);

              $json = json_encode($response->getAgentsListResult);
              $responseArray = json_decode($json,true);
            return $responseArray;
            
        } catch (\Throwable $th) {
            $json = json_encode($th);
            $responseArray = json_decode($json,true);
            return $responseArray;
        }
    }

    public function createContactDetails($data)
    {

        $this->getParametroFilter('WSDLDyn');
        try {
            $soapWrapper = new SoapWrapper;
            $soapWrapper->add('createContactDetails', function ($service) {
                $service->wsdl($this->url)
                ->trace(true);
              });
              
              $response = $soapWrapper->call('createContactDetails.createContactDetails', [
                'body' => $data
              ]);

              $json = json_encode($response->createContactDetailsResult);
              $responseArray = json_decode($json,true);
            return $responseArray;
            
        } catch (\Throwable $th) {
            $json = json_encode($th);
            $responseArray = json_decode($json,true);
            return $responseArray;
        }
    }

    public function createAgreementDetail($data)
    {
        $this->getParametroFilter('WSDLDyn');
        try {
            $soapWrapper = new SoapWrapper;
            $soapWrapper->add('createAgreementDetail', function ($service) {
                $service->wsdl($this->url)
                ->trace(true);
              });
              
              $response = $soapWrapper->call('createAgreementDetail.createAgreementDetail', [
                'body' => $data
              ]);

              $json = json_encode($response);
              $responseArray = json_decode($json,true);
            return $responseArray;
            
        } catch (\Throwable $th) {
            $json = json_encode($th);
            $responseArray = json_decode($json,true);
            return $responseArray;
        }
    }
}
