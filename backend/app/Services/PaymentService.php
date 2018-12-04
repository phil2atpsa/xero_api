<?php
/**
 * Created by PhpStorm.
 * User: phil_
 * Date: 2018/12/03
 * Time: 19:50
 */

namespace App\Services;


use XeroPHP\Application\PrivateApplication;
use XeroPHP\Models\Accounting\Payment;
use Carbon\Carbon;

class PaymentService
{
    /**
     *
     */
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
                throw  new \Exception("Invalid Invoice Date format should be Y-m-d");
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
           
            return  $this->payment->Payments->Payment->PaymentID;

        } catch(\Exception $ex){
            throw new \Exception($ex->getMessage());
        }


    }

}