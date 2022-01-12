<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CEO;
use Illuminate\Http\Request;
use App\Http\Resources\CEOResource;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Lcobucci\JWT\Validation\Validator;

class CEOController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ceos = CEO::all();

        return response ([
            'ceos' => CEOResource::collection($ceos), 
            'message' => 'Retrieved Succesfully'
        ], 200); 
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
            'name' => 'required|max:255',
            'company_name' => 'required|max:255',
            'year' => 'required|max:255',
            'company_headquarters' => 'required|max:255',
            'what_company_does' => 'required',
        ]);

        if ($validator->fails()){
            return response([
                'error' => $validator->errors(), 
                'message' => 'Validation Fail'
            ], 400);
        }

        $ceo = CEO::create($data);
        return response([
            'ceo' => new CEOResource($ceo),
            'message' => 'Created Successfully',
        ],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CEO  $cEO
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $ceoId)
    {
        $data = $request->all();
        $ceo = CEO::find($ceoId);
        if ($ceo){ 
            return response([
                'ceo' => $ceo,
                'message' => 'Retrieved Successfully'
            ],200);
        } else {
            return response([
                'message' => 'Retrieved Failed'
            ],400);
        }
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CEO  $cEO
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $ceoId)
    {
        $data = $request->all();

        $validator = FacadesValidator::make($data, [
            'name' => 'required|max:255',
            'company_name' => 'required|max:255',
            'year' => 'required|max:255',
            'company_headquarters' => 'required|max:255',
            'what_company_does' => 'required',
        ]);

        if ($validator->fails()){
            return response([
                'error' => $validator->errors(), 
                'message' => 'Validation Fail'
            ], 400);
        }
        $ceo = CEO::findOrFail($ceoId);
        $ceo->update($data);

        //$ceo = CEO::whereId($ceoId)->update($data);
        return response([
            'ceo' => $data,
            'message' => 'Updated Successfully',
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CEO  $cEO
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $ceoId)
    {
        //$data = $request->all();
        $res=CEO::find($ceoId)->delete();
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
