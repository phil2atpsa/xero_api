<?php
/**
 * Created by PhpStorm.
 * User: phil_
 * Date: 2018/11/28
 * Time: 14:07
 */

namespace App\Services;



use App\Collections;
use Carbon\Carbon;
use XeroPHP\Application\PrivateApplication;
use XeroPHP\Models\Accounting\Invoice;
use XeroPHP\Models\Accounting\Invoice\LineItem;


class InvoiceService
{

    const MODEL ='Accounting\\Invoice';
    private $xero = null;
    private $invoice;

    public function __construct(PrivateApplication $xero)
    {
        $this->xero = $xero;
        $this->invoice = new Invoice($this->xero);
    }


    /**
     * @param array $post
     * @return \SimpleXMLElement
     * @throws \Exception
     */
    public function saveBulk(array $post){

       // foreach($invoices as $post){

    
            try {
                $invoice = new Invoice($this->xero);
                $contact = $post['Contact'];
               $invoice->setContact($contact);
               $invoice->setType($post['Type']);
               $invoice->setDate($post['Date']);
               $invoice->setDueDate($post['DueDate']);

                if (isset($post['InvoiceNumber']))
                   $invoice->setInvoiceNumber($post['InvoiceNumber']);


                if (isset($post['BrandingThemeID'])) { //Field to hold Policy Number
                    //$this->invoice->setBrandingThemeID($post['BrandingThemeID']);
                }


                if (isset($post['Reference']))
                   $invoice->setReference($post['PolicyNumber']);

                if (isset($post['CurrencyCode']))
                   $invoice->setCurrencyCode($post['CurrencyCode']);


               $invoice->setStatus($post['Status']);

                if (isset($post['LineAmountTypes']))
                   $invoice->setLineAmountType($post['LineAmountTypes']);

                //$invoice->se

                foreach ($post['LineItems'] as $LineItems) {
                    $lineItem = new LineItem($this->xero);
                    $lineItem->setQuantity($LineItems['Quantity']);
                    $lineItem->setDescription($LineItems['Description']);
                    $lineItem->setUnitAmount($LineItems['UnitAmount']);
                    if (isset($LineItems['TaxType']))
                        $lineItem->setTaxType($LineItems['TaxType']);

                    if (isset($LineItems['TaxAmount']))
                        $lineItem->setTaxAmount((float)$LineItems['TaxAmount']);

                    if (isset($LineItems['AccountCode']))
                        $lineItem->setAccountCode($LineItems['AccountCode']);

                    $invoice->addLineItem($lineItem);
                    //$this->invoice->save();
                }

                $res = \simplexml_load_string($invoice->save()->getResponseBody());
                return $res->Invoices->Invoice->InvoiceID;

            } catch(\Exception $ex){
                throw new \Exception($ex->getMessage());

            }
    }


    public function change_invoice(string $invoiceID, string $reference, \DateTime $date )
    {
       $this->invoice->setInvoiceID($invoiceID);
       $this->invoice->setReference($reference);
       $this->invoice->setDate($date);
       $this->invoice->setDueDate($date);
       $this->invoice->save();
    }


   /**
     * @param array $post
     * @param string $id
     * @return \SimpleXMLElement
     * @throws \Exception
     */
    public function updateInvoice(array $post, string $id =null)
    {
       

        try {

           
            if (isset($post['Type']) && !in_array($post['Type'], [Invoice::INVOICE_TYPE_ACCPAY, Invoice::INVOICE_TYPE_ACCREC])) {
                throw  new \Exception("Invalid Invoice Type should
                    be one of : ".Invoice::INVOICE_TYPE_ACCPAY."|".Invoice::INVOICE_TYPE_ACCREC);

            }
            if(isset($post['Date'])) {
                $date = Carbon::createFromFormat("Y-m-d", $post['Date']);

                if ($date === false) {
                    throw  new \Exception("Invalid Invoice Date format should be Y-m-d\TH:i:s");
                }
                $this->invoice->setDate($date);


            }
            if(isset($post['DueDate'])) {
                $due_date = Carbon::createFromFormat("Y-m-d", $post['DueDate']);
                if ($due_date === false) {
                    throw  new \Exception("Invalid Invoice DueDate format should be Y-m-d\TH:i:s");
                }
                $this->invoice->setDueDate($due_date);
            }

            if (isset($post['Status']) && !in_array($post['Status'], [Invoice::INVOICE_STATUS_AUTHORISED,
                Invoice::INVOICE_STATUS_DELETED, Invoice::INVOICE_STATUS_DRAFT,
                Invoice::INVOICE_STATUS_PAID, Invoice::INVOICE_STATUS_SUBMITTED, Invoice::INVOICE_STATUS_VOIDED  ])) {
                throw  new \Exception("Invalid Invoice Status should
                    be one of : ".Invoice::INVOICE_STATUS_AUTHORISED.",".Invoice::INVOICE_STATUS_DELETED.",".Invoice::INVOICE_STATUS_DRAFT.",
                    ".Invoice::INVOICE_STATUS_PAID.",".Invoice::INVOICE_STATUS_SUBMITTED.",".Invoice::INVOICE_STATUS_VOIDED."");

            }
		
            if($id != null) {
                $this->invoice->setInvoiceID($id);
            }

            if(isset($post['Type']))
                $this->invoice->setType($post['Type']);


            if(isset($post['InvoiceNumber']))
                $this->invoice->setInvoiceNumber($post['InvoiceNumber']);


            if(isset($post['BrandingThemeID'])) {
                $this->invoice->setBrandingThemeID($post['BrandingThemeID']);
            }



            if(isset($post['Reference']))
                $this->invoice->setReference($post['Reference']);

            if(isset($post['CurrencyCode']))
                $this->invoice->setCurrencyCode($post['CurrencyCode']);


            $this->invoice->setStatus($post['Status']);

            if(isset($post['LineAmountTypes']))
                $this->invoice->setLineAmountTypes($post['LineAmountTypes']);

            if(isset($post['LineItems'])) {

                
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

                $this->invoice->addLineItem($lineItem);
            }
            }

            $this->invoice = \simplexml_load_string($this->invoice->save()->getResponseBody());

            return  $this->invoice->Invoices->Invoice->InvoiceID;


        } catch (\Exception  $ex){

           throw new \Exception($ex->getMessage());
        }

    }


    /**
     * @param array $post
     * @param string $id
     * @return \SimpleXMLElement
     * @throws \Exception
     */
    public function createInvoice(array $post, string $id =null)
    {
        $contact_service = new ContactService($this->xero);


        try {

            $contact = $contact_service->search($id);
            if (!in_array($post['Type'], [Invoice::INVOICE_TYPE_ACCPAY, Invoice::INVOICE_TYPE_ACCREC])) {
                throw  new \Exception("Invalid Invoice Type should
                    be one of : ".Invoice::INVOICE_TYPE_ACCPAY."|".Invoice::INVOICE_TYPE_ACCREC);

            }
            $date = Carbon::createFromFormat("Y-m-d", $post['Date']);

            if ( $date === false) {
                throw  new \Exception("Invalid Invoice Date format should be Y-m-d\TH:i:s");
            }
            $due_date = Carbon::createFromFormat("Y-m-d", $post['DueDate']);
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

           
            $this->invoice->setContact($contact);
            $this->invoice->setType($post['Type']);
            $this->invoice->setDate($date);
            $this->invoice->setDueDate($due_date);

            if(isset($post['InvoiceNumber']))
                $this->invoice->setInvoiceNumber($post['InvoiceNumber']);


            if(isset($post['BrandingThemeID'])) { //Field to hold Policy Number
                $this->invoice->setBrandingThemeID($post['BrandingThemeID']);
            }



            if(isset($post['Reference']))
                $this->invoice->setReference($post['Reference']);

            if(isset($post['CurrencyCode']))
                $this->invoice->setCurrencyCode($post['CurrencyCode']);


            $this->invoice->setStatus($post['Status']);

            if(isset($post['LineAmountTypes']))
               $this->invoice->setLineAmountTypes($post['LineAmountTypes']);

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

                $this->invoice->addLineItem($lineItem);
            }

            $this->invoice = \simplexml_load_string($this->invoice->save()->getResponseBody());

            return  $this->invoice->Invoices->Invoice->InvoiceID;


        } catch (\Exception  $ex){

           throw new \Exception($ex->getMessage());
        }

    }

    public function sync(Collections $collection, $verbose=false)
    {

        $contact_service = new ContactService($this->xero);

        $contact = $contact_service->verifyContact($collection->contact, $collection->mobile);
        
       

        $existing = $this->xero->load(self::MODEL)->where('InvoiceNumber="' . $collection->tid . '"')
            ->execute()->first();
      

        if (!$existing) {
            try {

                $invoice_date = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $collection->created_at);
                $due_date =  \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $invoice_date->addDays(14));
                
               

                $invoice = [
                    'Contact' => $contact,
                    'InvoiceNumber' => $collection->tid,
                    'Date' =>  $invoice_date,
                    'DueDate' => $due_date,
                    'Reference' => $collection->reference,
                    'CurrencyCode' => 'ZAR',
                    'Status' => 'AUTHORISED',
                    'Type' => Invoice::INVOICE_TYPE_ACCREC,
                    'SubTotal' => $collection->collection_amount,
                    'PolicyNumber' => $collection->policy_number,
                    'LineAmountTypes' => 'Exclusive',
                    'TotalTax' => 0,
                    'Total' => $collection->collection_amount,
                    'LineItems' => [[
                        'Description' => 'Unpaid premium for ' . $collection->policy_number,
                        'Quantity' => 1,
                        'UnitAmount' => $collection->collection_amount,
                        'TaxType' => 'OUTPUT',
                        'TaxAmount' => 0,
                        'LineAmount' => $collection->collection_amount,
                        'AccountCode' => 200
                    ]]


                ];
                
                //print_r($invoice); exit;
                
                // array_push($invoices, $invoice);
                $invoice_id =  $this->saveBulk($invoice);
                $collection->synced = 1;
                $collection->save();

                if($verbose) {
                    echo "{$invoice_id} Invoice {$collection->tid} Saved for Contact " . $contact->Name . "\n";
                    sleep(30);
                }

            } catch(\Exception $ex){
               throw new \Exception($ex->getMessage());
            }



        } else {
            $collection->synced = 1;
            $collection->save();

            if($verbose)
                echo "Invoice {$collection->tid} Skipped(Existing) for Contact " . $contact->Name . "\n";
        }

    }

    /**
     * @param string $contactID
     * @return array
     * @throws \XeroPHP\Remote\Exception
     */
    public function getContactInvoices(string $contactID) : array
    {
        return  $this->xero->load(InvoiceService::MODEL)
            ->where('Contact.ContactID = Guid("'.$contactID.'")')
            ->execute()
            ->getArrayCopy();

    }

    public function getAmountPayableTo(string $contactID) : float
    {
      return $this->getTotalAmount($contactID, 'ACCPAY');

    }

    public function getAmountDueBy(string $contactID): float{
        return $this->getTotalAmount($contactID, 'ACCREC');
    }

    private function getTotalAmount(string $contactID, string $type) :float{
        $amount = 0;
        $invoices = $this->xero->load(self::MODEL)
            ->where('Type ="'.$type.'" ')
            ->where('Contact.ContactID = Guid("'.$contactID.'")')
            ->execute()->getArrayCopy();

        foreach($invoices as $invoice){
            $amount += $invoice['Total'];
        }



        return $amount;


    }
    public  function getClientsInvoices(string $contactID) : array {
        return  $this->xero->load(self::MODEL)->where('Contact.ContactID = Guid("'.$contactID.'")')
                ->where('Type ="ACCREC" ')
            ->execute()->getArrayCopy();
    }

}