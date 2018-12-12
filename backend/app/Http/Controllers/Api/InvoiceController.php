<?php
/**
 * Created by PhpStorm.
 * User: phil_
 * Date: 2018/11/23
 * Time: 09:41
 */

namespace App\Http\Controllers\Api;
ini_set('max_execution_time',-1);

use App\Collections;
use App\Http\Controllers\Controller;
use App\Services\ContactService;
use App\Services\InvoiceService;
use App\Services\XeroService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use XeroPHP\Application\PrivateApplication;
use XeroPHP\Models\Accounting\Invoice;
use XeroPHP\Models\Accounting\Invoice\LineItem;
use PhpOffice\PhpSpreadsheet\IOFactory;



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

    public function upload_invoices(Request $request){
        //ini_set('max_execution_time', -1);
       // ini_set('memory_limit', -1);

        try {


            if (!$request->hasFile('template_file'))
            {
                throw new \Exception("No file Uploaded");
            }


            $template_file = $request->file('template_file');
            $file_name = $template_file->getClientOriginalName();
            $contents =  file_get_contents($template_file->getPathName());
            file_put_contents(storage_path($file_name), $contents);

            //$reader = IOFactory::createReader(storage_path($file_name));
            $spreadsheet = IOFactory::load(storage_path($file_name));
            $worksheet = $spreadsheet->getActiveSheet();

            $data = $worksheet->toArray();
            $length = count($data);
            //  $invoices = ['Invoices'][];




            // $chunk_size  = 39;
            // $section = ceil($length/$data );

            for($i=1;$i< $length; $i++) {
                $reference = $data[$i][2];
                //fi4269187

                $invoice_number = $data[$i][1]; //tid
                $reference = $data[$i][2]; //Reference
                $contact_name = $data[$i][3]; //Name
                $mobile = $data[$i][4]; //Cell
                $policy_number = $data[$i][5]; //policy Number
                $collection_amount = $data[$i][6]; // collection amount

                //Change of process Upload onto a database.
                $collections = Collections::where('tid',$invoice_number)
                ->get()->toArray();



                if(!$collections) {
                    $collections = new Collections();
                    $collections->tid = $invoice_number;
                    $collections->reference = $reference;
                    $collections->contact = $contact_name;
                    $collections->mobile = $mobile;
                    $collections->policy_number = $policy_number;
                    $collections->collection_amount = $collection_amount;
                    $collections->save();
                }







                /*  $contact_service = new ContactService($this->xero->getApplication());
                  $contact = $contact_service->verifyContact($contact_name, $mobile);


                    //print_r($contact->Name."\n");


                  $existing = $this->xero->load(InvoiceService::MODEL)->where('InvoiceNumber="' . $invoice_number . '"')
                      ->execute()->first();
                  if (!$existing) {

                     $invoice = [
                          'Contact' => $contact,
                          'InvoiceNumber' => $invoice_number,
                          'Date' => \Carbon\Carbon::createFromFormat("Y-m-d", \Carbon\Carbon::now()->format('Y-m-d')),
                          'DueDate' => \Carbon\Carbon::createFromFormat("Y-m-d", \Carbon\Carbon::now()->format('Y-m-d')),
                          'Reference' => $reference,
                          'CurrencyCode' => 'ZAR',
                          'Status' => 'AUTHORISED',
                          'Type' => Invoice::INVOICE_TYPE_ACCREC,
                          'SubTotal' => $collection_amount,
                          'PolicyNumber' => $policy_number,
                          'LineAmountTypes' => 'Exclusive',
                          'TotalTax' => 0,
                          'Total' => $collection_amount,
                          'LineItems' => [[
                              'Description' => 'Unpaid premium for ' . $policy_number,
                              'Quantity' => 1,
                              'UnitAmount' => $collection_amount,
                              'TaxType' => 'OUTPUT',
                              'TaxAmount' => 0,
                              'LineAmount' => $collection_amount,
                              'AccountCode' => 200
                          ]]


                      ];
                      // array_push($invoices, $invoice);
                      $this->invoice_service->saveBulk($invoice);

                  }*/

            }
            unlink(storage_path($file_name));
            //call_in_background("UploadInvoices");
            return response()->json(['success'=>1, 'message'=>'Invoices Uploaded Succesfully'], 200);

        } catch (\Exception $ex){
            return response()->json(['success'=>0, 'message'=>$ex->getMessage()], 200);
        }



    }

     /**
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update_invoice(Request $request, string $id): \Illuminate\Http\JsonResponse
    {
        

        $post = $request->all();
        $post = $post['Invoice'];
        try {
            return response()->json([
                'success'=> true,
                'message'=> config('api_response.xero.success_on_update'),
                'InvoiceID' => $this->invoice_service->updateInvoice($post, $id)
            ], 200);

        } catch(\Exception $ex){
            return  response()->json(['success'=> 'false', 'message' => $ex->getMessage()], 500);
        }


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
