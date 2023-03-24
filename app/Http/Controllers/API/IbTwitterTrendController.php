<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\IbTwitterTrend;
use App\Models\Procesos;
use Illuminate\Http\Request;
use App\Http\Resources\IbTwitterTrendResource;
use App\Http\Resources\ProcesosResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Lcobucci\JWT\Validation\Validator;

class IbTwitterTrendController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $maxProceso = Procesos::where('clasificacion', 'G')->where('terminado', '=', '1')->latest()->first();
        $trends = IbTwitterTrend::where('id_ps', $maxProceso->id) ->orderBy('score','DESC')
        ->skip(0)
        ->take(20)
        ->get();
        $fecha = $maxProceso['created_at'];
        $carbon_date = Carbon::parse($date);
        $carbon_date->addHours(-5);
        $maxProceso['created_at'] = $carbon_date;
        $data['trends']=$trends;
        $data['ps']=$maxProceso;
        return response (
            IbTwitterTrendResource::collection($data)
            , 200); 
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
     * @param  \App\Models\IbTwitterTrend  $ib_Twitter_Trend
     * @return \Illuminate\Http\Response
     */
    public function show(IbTwitterTrend $ib_Twitter_Trend)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Ib_Twitter_Trend  $ib_Twitter_Trend
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ib_Twitter_Trend $ib_Twitter_Trend)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\IbTwitterTrend  $ib_Twitter_Trend
     * @return \Illuminate\Http\Response
     */
    public function destroy(IbTwitterTrend $ib_Twitter_Trend)
    {
        //
    }
}
