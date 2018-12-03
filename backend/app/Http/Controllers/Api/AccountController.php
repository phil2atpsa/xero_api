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
use App\Services\XeroService;
use App\Services\AccountService;

class AccountController extends Controller
{
    /**
     * @var null|PrivateApplication
     */
    private $xero = null;
    private $account_service;



    /**
     * AccountController constructor.
     */
    public function __construct(XeroService $xeroService)
    {
        $this->xero = $xeroService;
        $this->account_service = new AccountService($this->xero->getApplication());
    }

    public function index()
    {
        $accounts = $this->xero->load(AccountService::MODEL)->execute();
        return response()->json($accounts->getArrayCopy(), 200);

    }

    public function show(string $id)
    {
        $account =  $this->xero->loadByGUID(AccountService::MODEL, $id);
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
         
            if (!in_array($post['Type'], AccountService::$account_type)) {
                throw new \Exception("Invalid Account type should be one of ".implode("|", AccountService::$account_type));
            }

            if (isset($post['Status']) && !in_array($post['Status'], AccountService::$account_status)) {
                throw new \Exception("Invalid Account Status should be one of ".implode("|", AccountService::$account_status));
            }


            if (isset($post['BankAccountType']) && !in_array($post['BankAccountType'], AccountService::$bank_account_type)) {
                throw new \Exception("Invalid Account Type should be one of ".implode("|", AccountService::$bank_account_type));
            }

           


            return response()->json([
                'success'=> true,
                'message'=> config('api_response.xero.success_on_create'),
                'AccountID' => $this->account_service->create($post)
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
           

            if (!in_array($post['Type'], AccountService::$account_type)) {
                throw new \Exception("Invalid Account type should be one of ".implode("|", AccountService::$account_type));
            }

            if (isset($post['Status']) && !in_array($post['Status'], AccountService::$account_status)) {
                throw new \Exception("Invalid Account Status should be one of ".implode("|", AccountService::$account_status));
            }


            if (isset($post['BankAccountType']) && !in_array($post['BankAccountType'], AccountService::$bank_account_type)) {
                throw new \Exception("Invalid Account Type should be one of ".implode("|", AccountService::$bank_account_type));
            }

           

            return response()->json([
                'success'=> true,
                'message'=> config('api_response.xero.success_on_update'),
                'AccountID' => $this->account_service->create($post, $id)
            ], 200);


        } catch( \Exception  $ex){

            return  response()->json(['success'=> 'false', 'message' => $ex->getMessage()], 500);
        }

    }
    
    public function account_classes(){
        $accounts = $this->xero->load(AccountService::MODEL)->execute();
        $classes = [];
        $return = [];
                
        foreach($accounts->getArrayCopy() as $account){
            if(!in_array($account['Class'], $classes)) {
                array_push($classes, $account['Class']);
                $obj = new \stdClass();
                $obj->name = $account['Class'];
                 array_push($return, $obj);
            }
        }
        
        usort($return, [$this,'_sortType']);
        return response()->json($return, 200);  
    }
    private function _sortType($obj1, $obj2){ 
        return strcmp($obj1->name, $obj2->name);
    }
    
    public function account_types(){
        $accounts = $this->xero->load(AccountService::MODEL)->execute();
        $types = [];
        $return = [];
        foreach($accounts->getArrayCopy() as $account){
            if(!in_array($account['Type'], $types)) {
                array_push($types, $account['Type']);
                 $obj = new \stdClass();
                 $obj->name = $account['Type'];
                 array_push($return, $obj);
            }
        }
         usort($return, [$this,'_sortType']);
        return response()->json($return, 200); 
    }

}
