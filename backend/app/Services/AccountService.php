<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AccountService
 *
 * @author phil_
 */

namespace App\Services;

use XeroPHP\Models\Accounting\Account;
use XeroPHP\Application\PrivateApplication;

class AccountService {

    //put your code here
    const MODEL = 'Accounting\\Account';

    private $account;

    /**
     * @var array
     */
    public static $account_type = [
        Account::ACCOUNT_TYPE_BANK,
        Account::ACCOUNT_TYPE_CURRENT,
        Account::ACCOUNT_TYPE_CURRLIAB,
        Account::ACCOUNT_TYPE_DEPRECIATN,
        Account::ACCOUNT_TYPE_DIRECTCOSTS,
        Account::ACCOUNT_TYPE_EXPENSE,
        Account::ACCOUNT_TYPE_FIXED,
        Account::ACCOUNT_TYPE_INVENTORY,
        Account::ACCOUNT_TYPE_LIABILITY,
        Account::ACCOUNT_TYPE_NONCURRENT,
        Account::ACCOUNT_TYPE_OTHERINCOME,
        Account::ACCOUNT_TYPE_OVERHEADS,
        Account::ACCOUNT_TYPE_PREPAYMENT,
        Account::ACCOUNT_TYPE_REVENUE,
        Account::ACCOUNT_TYPE_SALES,
        Account::ACCOUNT_TYPE_TERMLIAB,
        Account::ACCOUNT_TYPE_PAYGLIABILITY,
        Account::ACCOUNT_TYPE_SUPERANNUATIONEXPENSE,
        Account::ACCOUNT_TYPE_SUPERANNUATIONLIABILITY,
        Account::ACCOUNT_TYPE_WAGESEXPENSE,
        Account::ACCOUNT_TYPE_WAGESPAYABLELIABILITY
    ];

    /**
     * @var array
     */
    public static $account_status = [
        Account::ACCOUNT_STATUS_ACTIVE,
        Account::ACCOUNT_STATUS_ARCHIVED,
    ];

    /**
     * @var array
     */
    public static $bank_account_type = [
        Account::BANK_ACCOUNT_TYPE_BANK,
        Account::BANK_ACCOUNT_TYPE_CREDITCARD,
        Account::BANK_ACCOUNT_TYPE_PAYPAL
    ];

    public function __construct(PrivateApplication $xero) {
        $this->xero = $xero;
        $this->account = new Account($this->xero);
    }
    


    public function create(array $post, string $accountID = null) {
        
        try {
        $this->account->setCode($post['Code']);
        $this->account->setType($post['Type']);
        $this->account->setName($post['Name']);
        //  $account->setDescription($post['Description']);
        //  $account->setTaxType($post['TaxType']);
        
          if($accountID != null)
                $this->account->setAccountID($accountID);



        $account_satus = $post['Status'] ?? Account::ACCOUNT_STATUS_ACTIVE;
        
         $this->account->setStatus($account_satus);

        if ($post['Type'] == Account::ACCOUNT_TYPE_BANK) {
            if (isset($post['BankAccountNumber']))
                 $this->account->setBankAccountNumber($post['BankAccountNumber']);

            if (isset($post['BankAccountType']))
                 $this->account->setBankAccountType($post['BankAccountType']);

            if (isset($post['CurrencyCode']))
                 $this->account->setCurrencyCode($post['CurrencyCode']);
        }

         $this->account = \simplexml_load_string( $this->account->save()->getResponseBody());
         return  $this->account->Accounts->Account->AccountID;
        } catch(\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

}
