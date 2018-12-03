<?php
/**
 * Created by PhpStorm.
 * User: phil_
 * Date: 2018/11/28
 * Time: 14:07
 */

namespace App\Services;


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
     * @param string $id
     * @return \SimpleXMLElement
     * @throws \Exception
     */
    public function createInvoice(array $post, string $id)
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