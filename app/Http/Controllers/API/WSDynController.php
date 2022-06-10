<?php

namespace App\Http\Controllers\API;
set_time_limit(600);
use App\Http\Controllers\Controller;
use App\Http\Controllers\ParametroController;
use App\Http\Controllers\AgreementController;
use App\Http\Resources\WSDynResource;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Filtro;
use App\Http\Resources\FiltrosResource;


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

    /*public function createAgreementDB(Request $request){
        $data = $request->all();
        $agreement = new AgreementController();
        $agreementResponse = $agreement->store($data);

        return response (
            new WSDynResource($agreementResponse)
             , 200); 
    }*/


    public function registrarContrato(Request $request){
          
        /*$pdf = PDF::make('dompdf.wrapper');
    
        return $pdf->download('itsolutionstuff.pdf');*/
        //return PDF::loadHtml(file_get_contents(public_path().'/form9/form9.html'))->setPaper('letter')->save(public_path().'/form9/form9_es.pdf');
        $data = $request->all();
        $agreement = new AgreementController();
        $agreementResponse = $agreement->storeData($data);
        
        return response (
            $agreementResponse
             , 200); 
    }


    public function getOptionsCmr(Request $request){
          
        $data = $request->all();
        $agreement = new AgreementController();
        $body = null;
        $agreementResponse = $agreement->getResponseMethodWS($data['metodo'], $body);
        $labelArray = str_replace('get', '', $data['metodo']);
        $espanol = array_column($agreementResponse[$data['metodo']."Result"][$labelArray], $data['attr2ndLanguage']);
        $ingles = array_column($agreementResponse[$data['metodo']."Result"][$labelArray], $data['attrIngles']);
        $arrIdioma = array_combine($ingles, $espanol);
        $opcionesFiltro = Filtro::select('valor')->where('metodo', '=', $data['metodo'])->get()->toarray();
        if (count($opcionesFiltro) > 0){
            $opcionesFiltro = array_column($opcionesFiltro, 'valor');
            $arrIdioma = array_diff_key($arrIdioma, array_flip($opcionesFiltro));
        }
        return response (
            $arrIdioma
             , 200); 
    }
    
}
