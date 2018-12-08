<?php

namespace App\Http\Controllers\Api;

use App\Collections;
use App\Services\InvoiceService;
use App\Services\XeroService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CollectionsController extends Controller
{
    //

    private $xero;

    public function __construct(XeroService $xero)
    {
        $this->xero = $xero;
    }

    public function index() :  \Illuminate\Http\JsonResponse
    {
        return response()->json(Collections::where('synced',0)->get(), 200);
    }
    public function update(Request $request, int $id)  :  \Illuminate\Http\JsonResponse
    {
        $collection = Collections::find($id);
        $invoice_service = new InvoiceService($this->xero->getApplication());

        try
        {
            $invoice_service->sync($collection);
            return  response()->json(['success'=>1, 'message'=>'Successfully Synced'], 200);
        } catch(\Exception $ex){
            return  response()->json(['success'=>0, 'message'=>$ex->getMessage()], 500);
        }


    }
}
