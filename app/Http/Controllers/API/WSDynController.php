<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ParametroController;
use App\Http\Controllers\AgreementController;
use App\Http\Resources\WSDynResource;
use Illuminate\Http\Request;

class WSDynController extends Controller
{
    /**
     * Display a listing agentList From WS.
     *
     * @return \Illuminate\Http\Response
     */
    public function agentList()
    {
        $res = new ParametroController();
        $response = $res->getAgentsList();

        return response (
           new WSDynResource($response)
            , 200); 
    }

    public function createContactDetails(Request $request)
    {
        $data = $request->all();
        $res = new ParametroController();
        $response = $res->createContactDetails($data);

        return response (
           new WSDynResource($response)
            , 200); 
    }

    public function createAgreementDetail(Request $request)
    {
        $data = $request->all();
        $res = new ParametroController();
        $response = $res->createAgreementDetail($data);

        return response (
           new WSDynResource($response)
            , 200); 
    }

    public function createAgreementDB(Request $request){
        $data = $request->all();
        $agreement = new AgreementController();
        $agreementResponse = $agreement->store($data);

        return response (
            new WSDynResource($agreementResponse)
             , 200); 
    }
    
}
