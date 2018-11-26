<?php
/**
 * Created by PhpStorm.
 * User: phil_
 * Date: 2018/11/23
 * Time: 14:57
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use XeroPHP\Application\PrivateApplication;
use XeroPHP\Models\Accounting\BankTransaction;
use XeroPHP\Models\Accounting\BankTransaction\BankAccount;
use XeroPHP\Models\Accounting\BankTransaction\LineItem;

class BankTransactionsController extends  Controller
{
    protected $xero;

    static $transaction_type =[ BankTransaction::TYPE_RECEIVE,
        BankTransaction::TYPE_RECEIVE_OVERPAYMENT ,
        BankTransaction::TYPE_RECEIVE_PREPAYMENT ,
        BankTransaction::TYPE_SPEND             ,
        BankTransaction::TYPE_SPEND_OVERPAYMENT  ,
        BankTransaction::TYPE_SPEND_PREPAYMENT   ,
        BankTransaction::TYPE_RECEIVE_TRANSFER    ,
        BankTransaction::TYPE_SPEND_TRANSFER
    ];

    static $transaction_status =[ BankTransaction::BANK_TRANSACTION_STATUS_AUTHORISED,
        BankTransaction::BANK_TRANSACTION_STATUS_DELETED ];



    public function __construct()
    {
        $this->xero = new PrivateApplication(config('xero'));
    }


    public function index()
    {
        $bankTransactions = $this->xero->load('Accounting\\BankTransaction')->execute();
        return response()->json($bankTransactions->getArrayCopy(), 200);
    }

    public function show(string $id)
    {
        $bankTransaction = $this->xero->loadByGUID('Accounting\\BankTransaction', $id);
        return response()->json($bankTransaction->toStringArray(), 200);
    }

    public function store(Request $request)
    {
        $post = $request->all();

        try {
            $validator = Validator::make($post, [
                'BankTransaction' => 'required',


            ]);



            if($validator->fails()){
                throw new \Exception("Invalid Request");
            }

            $post = $post['BankTransaction'];

            $bankTransactions = new BankTransaction($this->xero);

            if(!in_array($post['Type'], static::$transaction_type)){
                throw new \Exception("Invalid Transaction Type should be one of ".implode("|", static::$transaction_type ));
            }
            if(!in_array($post['Status'], static::$transaction_status)){
                throw new \Exception("Invalid Transaction Type should be one of ".implode("|", static::$transaction_status ));
            }
            $date = Carbon::createFromFormat("Y-m-d\TH:i:s", $post['Date']);
            if ( $date === false) {
                throw  new \Exception("Invalid Transfer Date format should be Y-m-d\TH:i:s");
            }

            $bankTransactions->setType($post['Type']);

            $contact= $this->xero->loadByGUID("Accounting\\Contact", $post['ContactID']);
            $bankAccount = new BankAccount($this->xero);
            $bankAccount->setAccountID($post['BankAccountID']);
            $bankAccount->save();


            $bankTransactions->setContact($contact);
            $bankTransactions->setBankAccount($bankAccount);
            $bankTransactions->setDate($date);
            $bankTransactions->setStatus($post['Status']);
            $bankTransactions->setLineAmountType($post['LineAmountType']);
            foreach($post['LineItems'] as $LineItems){
                $lineItem = new LineItem($this->xero);
                $lineItem->setQuantity($LineItems['Quantity']);
                $lineItem->setDescription($LineItems['Description']);
                $lineItem->setUnitAmount($LineItems['UnitAmount']);
                if(isset($LineItems['TaxType']))
                    $lineItem->setTaxType($LineItems['TaxType']);

                if(isset($LineItems['LineAmount']))
                    $lineItem->setLineAmount((float) $LineItems['LineAmount']);

                if(isset($LineItems['AccountCode']))
                    $lineItem->setAccountCode( $LineItems['AccountCode']);

                $bankTransactions->addLineItem($lineItem);
            }


            $bankTransactions = \simplexml_load_string( $bankTransactions->save()->getResponseBody());


            return response()->json([
                'success'=> true,
                'message'=> config('api_response.xero.success_on_create'),
                'BankTransferID' => $bankTransactions->BankTransactions->BankTransaction->BankTransactionID
            ], 200);




        } catch( \Exception  $ex){

            return  response()->json(['success'=> 'false', 'message' => $ex->getMessage()], 500);
        }



    }

    public function update(Request $request, string $id)
    {
        $post = $request->all();

        try {
        $validator = Validator::make($post, [
            'BankTransaction' => 'required',


        ]);



        if($validator->fails()){
            throw new \Exception("Invalid Request");
        }

        $post = $post['BankTransaction'];

        $bankTransactions = new BankTransaction($this->xero);
        $bankTransactions->setBankTransactionID($id);

        if(!in_array($post['Type'], static::$transaction_type)){
            throw new \Exception("Invalid Transaction Type should be one of ".implode("|", static::$transaction_type ));
        }
        if(!in_array($post['Status'], static::$transaction_status)){
            throw new \Exception("Invalid Transaction Type should be one of ".implode("|", static::$transaction_status ));
        }
        $date = Carbon::createFromFormat("Y-m-d\TH:i:s", $post['Date']);
        if ( $date === false) {
            throw  new \Exception("Invalid Transfer Date format should be Y-m-d\TH:i:s");
        }

        $bankTransactions->setType($post['Type']);

        $contact= $this->xero->loadByGUID("Accounting\\Contact", $post['ContactID']);
        $bankAccount = new BankAccount($this->xero);
        $bankAccount->setAccountID($post['BankAccountID']);
        $bankAccount->save();


        $bankTransactions->setContact($contact);
        $bankTransactions->setBankAccount($bankAccount);
        $bankTransactions->setDate($date);
        $bankTransactions->setStatus($post['Status']);
        $bankTransactions->setLineAmountType($post['LineAmountType']);
        foreach($post['LineItems'] as $LineItems){
            $lineItem = new LineItem($this->xero);
            $lineItem->setQuantity($LineItems['Quantity']);
            $lineItem->setDescription($LineItems['Description']);
            $lineItem->setUnitAmount($LineItems['UnitAmount']);
            if(isset($LineItems['TaxType']))
                $lineItem->setTaxType($LineItems['TaxType']);

            if(isset($LineItems['LineAmount']))
                $lineItem->setLineAmount((float) $LineItems['LineAmount']);

            if(isset($LineItems['AccountCode']))
                $lineItem->setAccountCode( $LineItems['AccountCode']);

            $bankTransactions->addLineItem($lineItem);
        }


        $bankTransactions = \simplexml_load_string( $bankTransactions->save()->getResponseBody());


        return response()->json([
            'success'=> true,
            'message'=> config('api_response.xero.success_on_create'),
            'BankTransferID' => $bankTransactions->BankTransactions->BankTransaction->BankTransactionID
        ], 200);




          } catch(  \Exception  $ex){

            return  response()->json(['success'=> 'false', 'message' => $ex->getMessage()], 500);
        }



    }

}
