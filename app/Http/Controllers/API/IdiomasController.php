<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Idioma;
use Illuminate\Http\Request;
use App\Http\Resources\IdiomasResource;
use Illuminate\Support\Facades\Validator as FacadesValidator;


class IdiomasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records = Idioma::all();

        return response (
            IdiomasResource::collection($records)
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
            'language' => 'required|max:255',
            'section' => 'required|max:255',
            'element'=> 'required|max:255',
            'attr' => 'required|max:255',
            'label' => 'required|max:255',
            'status' => 'required|max:1',
        ]);

        if ($validator->fails()){
            return response([
                'error' => $validator->errors(), 
                'message' => 'Failure'
            ], 400);
        }

        $records = Idioma::create($data);
        return response([
            'idiomas' => IdiomasResource::make($records), 
            'message' => 'Created Successfully',
        ],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Idiomas  $tarifas
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $idiomaId)
    {
        $data = $request->all();
        $record = Idioma::find($idiomaId);
        if ($record){ 
            return response([
                'idiomas' => IdiomasResource::collection($idiomaId), 
                'message' => 'Retrieved Successfully'
            ],200);
        } else {
            return response([
                'message' => 'Retrieved Failed'
            ],400);
        }    
    }
    public function showpart(Request $request, $idiomaId, $sectionId)
    {
        $record = Idioma::where('language', '=', $idiomaId)
            ->where('section', '=', $sectionId)
            ->get();
        
        if ($record->count()>0) {
            return response(
                IdiomasResource::collection($record)
                ,200
            );
        } else {
            return response(
                0    
                ,400
            );
        }
    }
        
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Idioma  $tarifas
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idiomaId)
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
            $record = Idioma::findOrFail($idiomaId);
            $record->update($data);
            return response([
                'idioma' => $data,
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
    public function destroy(Request $request, $idiomaId)
    {
        $res=Idioma::find($idiomaId)->delete();
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
