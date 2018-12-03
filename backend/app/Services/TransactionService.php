<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TransactionService
 *
 * @author phil_
 */

namespace App\Services;

use XeroPHP\Application\PrivateApplication;
use XeroPHP\Models\Accounting\BankTransaction;
use XeroPHP\Models\Accounting\BankTransaction\BankAccount;
use XeroPHP\Models\Accounting\BankTransaction\LineItem;

class TransactionService {

    //put your code here

    public static $transaction_type = [ BankTransaction::TYPE_RECEIVE,
        BankTransaction::TYPE_RECEIVE_OVERPAYMENT,
        BankTransaction::TYPE_RECEIVE_PREPAYMENT,
        BankTransaction::TYPE_SPEND,
        BankTransaction::TYPE_SPEND_OVERPAYMENT,
        BankTransaction::TYPE_SPEND_PREPAYMENT,
        BankTransaction::TYPE_RECEIVE_TRANSFER,
        BankTransaction::TYPE_SPEND_TRANSFER
    ];
    public static $transaction_status = [ BankTransaction::BANK_TRANSACTION_STATUS_AUTHORISED,
        BankTransaction::BANK_TRANSACTION_STATUS_DELETED];
    private $xero = null;
    private $transactions;

    public function __construct(PrivateApplication $xero) {
        $this->xero = $xero;
        $this->transactions = new BankTransaction($this->xero);
    }

    public function create($post, $transactionID = null) {
        try {

            if (!in_array($post['Type'], static::$transaction_type)) {
                throw new \Exception("Invalid Transaction Type should be one of " . implode("|", static::$transaction_type));
            }
            if (!in_array($post['Status'], static::$transaction_status)) {
                throw new \Exception("Invalid Transaction Type should be one of " . implode("|", static::$transaction_status));
            }
            $date = Carbon::createFromFormat("Y-m-d", $post['Date']);
            if ($date === false) {
                throw new \Exception("Invalid Transfer Date format should be Y-m-d");
            }

            $bankTransactions->setType($post['Type']);

            $contact = $this->xero->loadByGUID(ContactService::MODEL, $post['ContactID']);
            $bankAccount = new BankAccount($this->xero);
            $bankAccount->setAccountID($post['BankAccountID']);
            $bankAccount->save();

            if($transactionID ! null){
                 $this->transactions->setTransactionID($transactionID);
            }
            $this->transactions->setContact($contact);
            $this->transactions->setBankAccount($bankAccount);
            $this->transactions->setDate($date);
            $this->transactions->setStatus($post['Status']);
            $this->transactions->setLineAmountType($post['LineAmountType']);
            foreach ($post['LineItems'] as $LineItems) {
                $lineItem = new LineItem($this->xero);
                $lineItem->setQuantity($LineItems['Quantity']);
                $lineItem->setDescription($LineItems['Description']);
                $lineItem->setUnitAmount($LineItems['UnitAmount']);
                if (isset($LineItems['TaxType']))
                    $lineItem->setTaxType($LineItems['TaxType']);

                if (isset($LineItems['LineAmount']))
                    $lineItem->setLineAmount((float) $LineItems['LineAmount']);

                if (isset($LineItems['AccountCode']))
                    $lineItem->setAccountCode($LineItems['AccountCode']);

                $this->transactions->addLineItem($lineItem);
            }


            $this->transactions = \simplexml_load_string($this->transactions->save()->getResponseBody());
            return  $this->transactions->BankTransactions->BankTransaction->BankTransactionID;
        } catch (\Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

}
