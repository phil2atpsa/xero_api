<?php

namespace App\Http\Controllers\Api;

use App\Collections;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CollectionsController extends Controller
{
    //
    
    public function index() :  \Illuminate\Http\JsonResponse
    {
        return response()->json(Collections::where('synced',0)->get(), 200);
    }
}
