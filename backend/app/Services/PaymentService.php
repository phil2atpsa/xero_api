<?php

/**
 * Created by PhpStorm.
 * User: phil_
 * Date: 2018/12/03
 * Time: 19:50
 */
namespace App\Services;


use App\Collections;
use App\Payments;
use Sujip\Guid\Guid;
use XeroPHP\Application\PrivateApplication;
use XeroPHP\Models\Accounting\Payment;
use Carbon\Carbon;
use XeroPHP\Remote\Exception;


class PaymentService {


    const MODEL = 'Accounting\\Payment';

    /**
     * @var Payment
     */
    private $payment;

    /**
     * PaymentService constructor.
     * @param PrivateApplication $xero
     */
    public function __construct(PrivateApplication $xero)
    {
        $this->xero = $xero;
        $this->payment = new Payment($this->xero);
    }

    public function makePayment(array $post)
    {
        try {

            $this->payment->setAmount($post['Amount']);
            $date = Carbon::createFromFormat("Y-m-d", $post['Date']);

            if ($date === false) {
                throw new \Exception("Invalid Invoice Date format should be Y-m-d");
            }

            $this->payment->setDate($date);
            //$this->payment->setStatus()
            $account = $this->xero->loadByGUID(AccountService::MODEL, $post['AccountID']);
            $invoice = $this->xero->loadByGUID(InvoiceService::MODEL, $post['InvoiceID']);
            $this->payment->setAccount($account);
            $this->payment->setInvoice($invoice);
            $this->payment->setIsReconciled(True);
            $this->payment->setReference($post['reference']);

            $this->payment = \simplexml_load_string($this->payment->save()->getResponseBody());

            return $this->payment->Payments->Payment->PaymentID;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }


    /**
     *
     * The account we pay from is hard coded for now -- remember to change on live System
     * @param $policyNumber
     * @param $amount
     * @param $reference
     * @return \SimpleXMLElement
     * @throws \XeroPHP\Remote\Exception
     */
    public function kp_payment($policyNumber, $amount,$reference,  $bank, $method) {

        $invoice_service = new InvoiceService($this->xero);

        if(Collections::Where('policy_number', $policyNumber)->exists()){
            $collection = Collections::Where('policy_number', $policyNumber)->first();


            if(!$collection->synced){
                $invoice_service->sync($collection);
            }

            $this->payment->setAmount($amount);
            $date = Carbon::createFromFormat("Y-m-d", Carbon::now()->format('Y-m-d'));
            $this->payment->setDate($date);

            $account = $this->xero->load(AccountService::MODEL)
                ->where('Code = "090"')
                ->execute()->first();


            $invoice = $this->xero->load(InvoiceService::MODEL)
                ->where('Reference = "' . $policyNumber . '"')
                ->where('Type ="ACCREC"')
                ->execute()->first();

            $this->payment->setAccount($account);
            $this->payment->setInvoice($invoice);
            $this->payment->setIsReconciled(True);
            $this->payment->setReference(strtoupper($bank)." - ".strtoupper($method)."  ".$reference);

            $this->payment = \simplexml_load_string($this->payment->save()->getResponseBody());

            return $this->payment->Payments->Payment->PaymentID;

        } else {

            $payment = new Payments();
            $payment->policy_number = $policyNumber;
            $payment->amount = $amount;
            $payment->reference = $reference;
            $payment->bank = $bank;
            $payment->method = $method;
            $payment->save();
            $guid = new Guid();
            return [$guid->create()];
        }



    }

}
