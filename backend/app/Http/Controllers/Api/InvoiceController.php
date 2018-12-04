<?php
/**
 * Created by PhpStorm.
 * User: phil_
 * Date: 2018/11/23
 * Time: 09:41
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Services\InvoiceService;
use App\Services\XeroService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use XeroPHP\Application\PrivateApplication;
use XeroPHP\Models\Accounting\Invoice;
use XeroPHP\Models\Accounting\Invoice\LineItem;

class InvoiceController extends Controller
{
    /**
     * @var null|PrivateApplication
     */
    private $xero = null;
    private $invoice_service;

    /**
     * InvoiceController constructor.
     * @param XeroService $xeroService
     */
    public function __construct(XeroService $xeroService)
    {
        $this->xero = $xeroService;
        $this->invoice_service = new InvoiceService($this->xero->getApplication());
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \XeroPHP\Remote\Exception
     */
    public function index() : \Illuminate\Http\JsonResponse
    {
        $invoices = $this->xero
                ->load(InvoiceService::MODEL)
                ->where('Type ="ACCREC"')
                ->execute()
                ->getArrayCopy();
        return response()->json( $invoices , 200);
    }
    public function payable_invoice() : \Illuminate\Http\JsonResponse
    {
         $invoices = $this->xero
                ->load(InvoiceService::MODEL)
                ->where('Type ="ACCREC"')
                ->where('Status ="AUTHORISED"')
                ->execute()
                ->getArrayCopy();
        return response()->json( $invoices , 200);
        
    }
    
    public function show($id) : \Illuminate\Http\JsonResponse
    {
      
        return response()->json( $this->xero
                ->loadByGUID(InvoiceService::MODEL, $id)
               ->toStringArray() , 200);
    }

    /**
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function create_invoice(Request $request, string $id): \Illuminate\Http\JsonResponse
    {
        $contact = null;

        $post = $request->all();
        $post = $post['Invoice'];
        try {
            return response()->json([
                'success'=> true,
                'message'=> config('api_response.xero.success_on_create'),
                'InvoiceID' => $this->invoice_service->createInvoice($post, $id)
            ], 200);

        } catch(\Exception $ex){
            return  response()->json(['success'=> 'false', 'message' => $ex->getMessage()], 500);
        }


    }
    /**
     * @
     */
    public function contact_invoices(string $contactID) : \Illuminate\Http\JsonResponse{
            return response()->json( $this->invoice_service->getClientsInvoices($contactID) , 200);
    }

}
