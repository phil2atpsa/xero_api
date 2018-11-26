<?php
/**
 * Created by PhpStorm.
 * User: phil_
 * Date: 2018/11/23
 * Time: 12:38
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use XeroPHP\Application\PrivateApplication;
use XeroPHP\Models\Accounting\Account;

class AccountController extends Controller
{
    /**
     * @var null|PrivateApplication
     */
    private $xero = null;

    /**
     * @var array
     */
    private  static $account_type = [
        Account::ACCOUNT_TYPE_BANK,
        Account::ACCOUNT_TYPE_CURRENT,
        Account::ACCOUNT_TYPE_CURRLIAB,
        Account::ACCOUNT_TYPE_DEPRECIATN,
        Account::ACCOUNT_TYPE_DIRECTCOSTS,
        Account::ACCOUNT_TYPE_EXPENSE,
        Account::ACCOUNT_TYPE_FIXED ,
        Account::ACCOUNT_TYPE_INVENTORY,
        Account::ACCOUNT_TYPE_LIABILITY,
        Account::ACCOUNT_TYPE_NONCURRENT,
        Account::ACCOUNT_TYPE_OTHERINCOME,
        Account::ACCOUNT_TYPE_OVERHEADS,
        Account::ACCOUNT_TYPE_PREPAYMENT,
        Account::ACCOUNT_TYPE_REVENUE,
        Account::ACCOUNT_TYPE_SALES,
        Account::ACCOUNT_TYPE_TERMLIAB,
        Account::ACCOUNT_TYPE_PAYGLIABILITY ,
        Account::ACCOUNT_TYPE_SUPERANNUATIONEXPENSE  ,
        Account::ACCOUNT_TYPE_SUPERANNUATIONLIABILITY,
        Account::ACCOUNT_TYPE_WAGESEXPENSE ,
        Account::ACCOUNT_TYPE_WAGESPAYABLELIABILITY
    ];
    /**
     * @var array
     */
    private  static $account_status = [
        Account::ACCOUNT_STATUS_ACTIVE,
        Account::ACCOUNT_STATUS_ARCHIVED,
    ];
    /**
     * @var array
     */
    private static $bank_account_type = [
            Account::BANK_ACCOUNT_TYPE_BANK,
            Account::BANK_ACCOUNT_TYPE_CREDITCARD,
            Account::BANK_ACCOUNT_TYPE_PAYPAL
    ];



    /**
     * AccountController constructor.
     */
    public function __construct()
    {
        $this->xero = new PrivateApplication(config('xero'));
    }

    public function index()
    {
        $accounts = $this->xero->load('Accounting\\Account')->execute();
        return response()->json($accounts->getArrayCopy(), 200);

    }

    public function show(string $id)
    {
        $account =  $this->xero->loadByGUID('Accounting\\Account', $id);
        return response()->json($account->toStringArray(), 200);

    }

    public function store(Request $request)
    {

        $post = $request->all();

        try {
            $validator = Validator::make($post, [
                'Type' => 'required',
                'Name' => 'required',

            ]);
            if($validator->fails()){
                throw new \Exception("Required Fields Missing : Type|Name");
            }
            $account = new Account($this->xero);
            if (!in_array($post['Type'], static::$account_type)) {
                throw new \Exception("Invalid Account type should be one of ".implode("|", static::$account_type));
            }

            if (isset($post['Status']) && !in_array($post['Status'], static::$account_status)) {
                throw new \Exception("Invalid Account Status should be one of ".implode("|", static::$account_status));
            }


            if (isset($post['BankAccountType']) && !in_array($post['BankAccountType'], static::$bank_account_type)) {
                throw new \Exception("Invalid Account Type should be one of ".implode("|", static::$bank_account_type));
            }

            $account->setCode($post['Code']);
            $account->setType($post['Type']);
            $account->setName($post['Name']);
          //  $account->setDescription($post['Description']);
          //  $account->setTaxType($post['TaxType']);



            $account_satus = $post['Status'] ?? Account::ACCOUNT_STATUS_ACTIVE;
            $account->setStatus($account_satus);

            if($post['Type'] == Account::ACCOUNT_TYPE_BANK) {
                if(isset($post['BankAccountNumber']))
                    $account->setBankAccountNumber($post['BankAccountNumber']);

                if(isset($post['BankAccountType']))
                    $account->setBankAccountType($post['BankAccountType']);

                if(isset($post['CurrencyCode']))
                    $account->setCurrencyCode($post['CurrencyCode']);


            }

            $account = \simplexml_load_string($account->save()->getResponseBody());


            return response()->json([
                'success'=> true,
                'message'=> config('api_response.xero.success_on_create'),
                'AccountID' => $account->Accounts->Account->AccountID
            ], 200);


        } catch(\Exception  $ex){

            return  response()->json(['success'=> 'false', 'message' => $ex->getMessage()], 500);
        }

    }

    public function update(Request $request, string $id)  :  \Illuminate\Http\JsonResponse
    {
        $post = $request->all();

        try {
            $validator = Validator::make($post, [
                'Type' => 'required',
                'Name' => 'required',

            ]);
            if($validator->fails()){
                throw new \Exception("Required Fields Missing : Type|Name");
            }
            $account = new Account($this->xero);
            $account->setAccountID($id);

            if (!in_array($post['Type'], static::$account_type)) {
                throw new \Exception("Invalid Account type should be one of ".implode("|", static::$account_type));
            }

            if (isset($post['Status']) && !in_array($post['Status'], static::$account_status)) {
                throw new \Exception("Invalid Account Status should be one of ".implode("|", static::$account_status));
            }


            if (isset($post['BankAccountType']) && !in_array($post['BankAccountType'], static::$bank_account_type)) {
                throw new \Exception("Invalid Account Type should be one of ".implode("|", static::$bank_account_type));
            }

            $account->setCode($post['Code']);
            $account->setType($post['Type']);
            $account->setName($post['Name']);
            //  $account->setDescription($post['Description']);
            //  $account->setTaxType($post['TaxType']);



            $account_satus = $post['Status'] ?? Account::ACCOUNT_STATUS_ACTIVE;
            $account->setStatus($account_satus);

            if($post['Type'] == Account::ACCOUNT_TYPE_BANK) {
                if(isset($post['BankAccountNumber']))
                    $account->setBankAccountNumber($post['BankAccountNumber']);

                if(isset($post['BankAccountType']))
                    $account->setBankAccountType($post['BankAccountType']);

                if(isset($post['CurrencyCode']))
                    $account->setCurrencyCode($post['CurrencyCode']);


            }

            $account = \simplexml_load_string($account->save()->getResponseBody());


            return response()->json([
                'success'=> true,
                'message'=> config('api_response.xero.success_on_update'),
                'AccountID' => $account->Accounts->Account->AccountID
            ], 200);


        } catch( \Exception  $ex){

            return  response()->json(['success'=> 'false', 'message' => $ex->getMessage()], 500);
        }

    }

}
