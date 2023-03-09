<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Procesos;
use Illuminate\Http\Request;
use App\Http\Resources\ProcesosResource;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Lcobucci\JWT\Validation\Validator;

class ProcesosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $procesos = Procesos::where('clasificacion', '=', 'G')->where('terminado', '=', '1')::get();

        return response (
            ProcesosResource::collection($procesos)
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
     * @param  \App\Models\Procesos  $procesos
     * @return \Illuminate\Http\Response
     */
    public function show(Procesos $procesos)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Procesos  $procesos
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Procesos $procesos)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Procesos  $procesos
     * @return \Illuminate\Http\Response
     */
    public function destroy(Procesos $procesos)
    {
        //
    }
}
