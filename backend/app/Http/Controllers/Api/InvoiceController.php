<?php
/**
 * Created by PhpStorm.
 * User: phil_
 * Date: 2018/11/23
 * Time: 09:41
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
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

    /**
     * InvoiceController constructor.
     */
    public function __construct()
    {
        $this->xero = new PrivateApplication(config('xero'));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \XeroPHP\Remote\Exception
     */
    public function index() : \Illuminate\Http\JsonResponse
    {
        $invoices = $this->xero->load('Accounting\\Invoice')->execute();
        return response()->json($invoices->getArrayCopy(), 200);
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


            if(preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}/', $id)) {
                $contact = $this->xero->load('Accounting\\Contact')
                    ->where('EmailAddress = "'.$id.'"')
                    ->execute()
                    ->first();
            } else {
                $contact = $this->xero->loadByGUID('Accounting\\Contact', $id);
            }

            if (!in_array($post['Type'], [Invoice::INVOICE_TYPE_ACCPAY, Invoice::INVOICE_TYPE_ACCREC])) {
                throw  new \Exception("Invalid Invoice Type should
                    be one of : ".Invoice::INVOICE_TYPE_ACCPAY."|".Invoice::INVOICE_TYPE_ACCREC);

            }
            $date = Carbon::createFromFormat("Y-m-d\TH:i:s", $post['Date']);

            if ( $date === false) {
                throw  new \Exception("Invalid Invoice Date format should be Y-m-d\TH:i:s");
            }
            $due_date = Carbon::createFromFormat("Y-m-d\TH:i:s", $post['DueDate']);
            if ( $due_date === false) {
                throw  new \Exception("Invalid Invoice DueDate format should be Y-m-d\TH:i:s");
            }

            if (!in_array($post['Status'], [Invoice::INVOICE_STATUS_AUTHORISED,
                Invoice::INVOICE_STATUS_DELETED, Invoice::INVOICE_STATUS_DRAFT,
                Invoice::INVOICE_STATUS_PAID, Invoice::INVOICE_STATUS_SUBMITTED, Invoice::INVOICE_STATUS_VOIDED  ])) {
                throw  new \Exception("Invalid Invoice Status should
                    be one of : ".Invoice::INVOICE_STATUS_AUTHORISED.",".Invoice::INVOICE_STATUS_DELETED.",".Invoice::INVOICE_STATUS_DRAFT.",
                    ".Invoice::INVOICE_STATUS_PAID.",".Invoice::INVOICE_STATUS_SUBMITTED.",".Invoice::INVOICE_STATUS_VOIDED."");

            }

            $invoice = new Invoice($this->xero);
            $invoice->setContact($contact);
            $invoice->setType($post['Type']);
            $invoice->setDate($date);
            $invoice->setDueDate($due_date);

            if(isset($post['InvoiceNumber']))
                $invoice->setInvoiceNumber($post['InvoiceNumber']);


            if(isset($post['Reference']))
                $invoice->setReference($post['Reference']);

            if(isset($post['CurrencyCode']))
                $invoice->setCurrencyCode($post['CurrencyCode']);


            $invoice->setStatus($post['Status']);

            if(isset($post['LineAmountTypes']))
                $invoice->setLineAmountTypes($post['LineAmountTypes']);

            foreach($post['LineItems'] as $LineItems){
                $lineItem = new LineItem($this->xero);
                $lineItem->setQuantity($LineItems['Quantity']);
                $lineItem->setDescription($LineItems['Description']);
                $lineItem->setUnitAmount($LineItems['UnitAmount']);
                if(isset($LineItems['TaxType']))
                 $lineItem->setTaxType($LineItems['TaxType']);

                if(isset($LineItems['TaxAmount']))
                    $lineItem->setTaxAmount((float) $LineItems['TaxAmount']);

                if(isset($LineItems['AccountCode']))
                    $lineItem->setAccountCode( $LineItems['AccountCode']);

                $invoice->addLineItem($lineItem);
            }

            $invoice = \simplexml_load_string($invoice->save()->getResponseBody());




            return response()->json([
                'success'=> true,
                'message'=> config('api_response.xero.success_on_create'),
                'InvoiceID' => $invoice->Invoices->Invoice->InvoiceID
            ], 200);






        } catch (\Exception  $ex){

            return  response()->json(['success'=> 'false', 'message' => $ex->getMessage()], 500);
        }


    }

}
