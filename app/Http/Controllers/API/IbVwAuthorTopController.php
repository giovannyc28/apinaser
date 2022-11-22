<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\IbVwAuthorTop;
use Illuminate\Http\Request;
use App\Http\Resources\IbVwAuthorTopResource;

class IbVwAuthorTopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
     * @param  \App\Models\IbVwAuthorTop  $ibVwAuthorTop
     * @return \Illuminate\Http\Response
     */
    public function show(IbVwAuthorTop $ibVwAuthorTop)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\IbVwAuthorTop  $ibVwAuthorTop
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, IbVwAuthorTop $ibVwAuthorTop)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\IbVwAuthorTop  $ibVwAuthorTop
     * @return \Illuminate\Http\Response
     */
    public function destroy(IbVwAuthorTop $ibVwAuthorTop)
    {
        //
    }


    public function showpart(Request $request, $idPs, $score)
    {
        $record = IbVwAuthorTop::where('id_ps', '=', $idPs)
            ->where('score', '=', $score)
            ->get();
        
        if ($record->count()>0) {
            return response(
                IbVwAuthorTopResource::collection($record)
                ,200
            );
        } else {
            return response(
                0    
                ,400
            );
        }
    }

    public function showByPrcoess(Request $request, $idPs)
    {
        $record = IbVwAuthorTop::where('id_ps', '=', $idPs)
            ->get();
        
        if ($record->count()>0) {
            return response(
                IbVwAuthorTopResource::collection($record)
                ,200
            );
        } else {
            return response(
                0    
                ,400
            );
        }
    }
}
