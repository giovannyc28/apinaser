<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Tarifas;
use Illuminate\Http\Request;
use App\Http\Resources\TarifasResource;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Lcobucci\JWT\Validation\Validator;

class TarifasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tarifas = Tarifas::all();

        return response (
            TarifasResource::collection($tarifas)
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
        $data = $request->all();

        $validator = FacadesValidator::make($data, [
            'plan_type' => 'required|max:255|unique:tarifas',
            'subscription_fee' => 'required|numeric',
            'yearly_fee' => 'required|numeric',
            'halfyear_fee' => 'required|numeric',
            'quarterly_fee' => 'required|numeric',
            'monthly_fee' => 'required|numeric',
            'anualmultiple_pays' => 'nullable|numeric',
            'cant_person' => 'nullable|numeric',
            'value_kid' => 'nullable|numeric',
            'value_adult' => 'nullable|numeric',
        ]);

        if ($validator->fails()){
            return response([
                'error' => $validator->errors(), 
                'message' => 'Failure'
            ], 400);
        }

        $tarifas = Tarifas::create($data);
        return response([
            'tarifas' => TarifasResource::make($tarifas), 
            'message' => 'Created Successfully',
        ],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tarifas  $tarifas
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $tarifaId)
    {
        $data = $request->all();
        $tarifas = Tarifas::find($tarifaId);
        if ($tarifas){ 
            return response([
                'tarifas' => TarifasResource::collection($tarifas), 
                'message' => 'Retrieved Successfully'
            ],200);
        } else {
            return response([
                'message' => 'Retrieved Failed'
            ],400);
        }    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tarifas  $tarifas
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $tarifaId)
    {
        $data = $request->all();
        $validator = FacadesValidator::make($data, [
            'subscription_fee' => 'numeric',
            'yearly_fee' => 'numeric',
            'halfyear_fee' => 'numeric',
            'quarterly_fee' => 'numeric',
            'monthly_fee' => 'numeric',
            'anualmultiple_pays' => 'nullable|numeric',
            'cant_person' => 'nullable|numeric',
            'value_kid' => 'nullable|numeric',
            'value_adult' => 'nullable|numeric',
        ]);
        if ($validator->fails()){
            return response([
                'error' => $validator->errors(), 
                'message' => 'Failure'
            ], 400);
        }

        try {
            $tarifa = Tarifas::findOrFail($tarifaId);
            $tarifa->update($data);
            return response([
                'tarifa' => $data,
                'message' => 'Updated Successfully',
            ],200);

        } catch (\Throwable $th) {
            return response([
                'message' => 'Failed'
            ], 400);
        }
        
        //$tarifa->update($data);
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tarifas  $tarifas
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $tarifaId)
    {
        $res=Tarifas::find($tarifaId)->delete();
            if ($res){
                $data=[
                    'status'=>'1',
                    'sg'=>'success'
                ];
                $status = 200;
            } else {
                $data=[
                    'status'=>'0',
                    'msg'=>'fail'
                ];
                $status = 400;
            }
        return response(['message' => $data ],$status);
    }
}
