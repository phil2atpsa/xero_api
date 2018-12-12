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

    public function collection_change(Request $request, $id)
    {
        $collection = Collections::find($id);
        $post = $request->all();
        $reference = $post['reference'];
        $date =  \Carbon\Carbon::createFromFormat("Y-m-d", $post['date']);


        $collection->policy_number = $reference;
        $collection->created_at = $post['date'];
        $collection->save();
        $invoice_service = new InvoiceService($this->xero->getApplication());

        if($collection->synced)
        {
            $invoice = $this->xero->load(InvoiceService::MODEL)->where('Reference = "'.$reference.'"')
                ->execute()->first();
            $invoice_service->change_invoice($invoice->invoiceID, $reference, $date);
        } else
        {
            $invoice_service->sync($collection);
        }



        return  response()->json(['success'=>1, 'message'=> config('api_response.xero.success_on_update')], 200);




    }

    public function show(int $id) :  \Illuminate\Http\JsonResponse
    {
        return response()->json(Collections::find($id), 200);
    }
}
