<?php
/**
 * Created by PhpStorm.
 * User: phil_
 * Date: 2018/12/04
 * Time: 09:10
 */

namespace App\Services;


use Carbon\Carbon;
use XeroPHP\Application\PrivateApplication;
use XeroPHP\Models\Accounting\BankTransfer;
use XeroPHP\Models\Accounting\BankTransfer\FromBankAccount;
use XeroPHP\Models\Accounting\BankTransfer\ToBankAccount;

class BankTransferService
{
    private $xero;
    private $bank_transfer;

    const MODEL="Accounting\\BankTransfer";

    public function __construct(PrivateApplication $xero)
    {
        $this->xero = $xero;
        $this->bank_transfer = new BankTransfer($this->xero);
    }

    public function create(array $post)
    {
        try {
            $date = Carbon::createFromFormat("Y-m-d", $post['Date']);

            if ($date === false) {
                throw  new \Exception("Invalid Transfer Date format should be Y-m-d\TH:i:s");
            }
            $this->bank_transfer->setDate($date);

            $fromBankAccount = new FromBankAccount($this->xero);
            $fromBankAccount->setCode($post['FromBankAccount']['Code']);
            $fromBankAccount->save();

            /*  $fromBankAccount = $this->xero->load("Accounting\\BankTransfer\\FromBankAccount")
                  ->where('Code = "'.$post['FromBankAccount']['Code'].'" ')->execute()->first();*/

            $this->bank_transfer->setFromBankAccount($fromBankAccount);

            /*  $toBankAccount = $this->xero->load("Accounting\\BankTransfer\\ToBankAccount")
                  ->where('Code = "'.$post['ToBankAccount']['Code'].'" ')->execute()->first();*/

            $toBankAccount = new ToBankAccount($this->xero);
            $toBankAccount->setCode($post['ToBankAccount']['Code']);
            $toBankAccount->save();


            $this->bank_transfer->setToBankAccount($toBankAccount);
            $this->bank_transfer->setAmount($post['Amount']);
            $this->bank_transfer = \simplexml_load_string($this->bank_transfer->save()->getResponseBody());
            return $this->bank_transfer->BankTransfers->BankTransfer->BankTransferID;

        } catch(\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }




}