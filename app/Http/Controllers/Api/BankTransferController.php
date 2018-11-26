<?php
/**
 * Created by PhpStorm.
 * User: phil_
 * Date: 2018/11/23
 * Time: 14:12
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use XeroPHP\Application\PrivateApplication;
use XeroPHP\Models\Accounting\BankTransfer;
use XeroPHP\Models\Accounting\BankTransfer\FromBankAccount;
use XeroPHP\Models\Accounting\BankTransfer\ToBankAccount;

class BankTransferController extends  Controller
{


    /**
     * @var PrivateApplication
     */
    private $xero;

    /**
     * BankTransferController constructor.
     */
    public function __construct()
    {
        $this->xero = new PrivateApplication(config('xero'));
    }

    public function index(){
        $transfers = $this->xero->load('Accounting\\BankTransfer')->execute();
        return response()->json($transfers->getArrayCopy(), 200);
    }
    public function show(string $id){
        $transfer = $this->xero->loadByGUID('Accounting\\BankTransfer',$id );
        return response()->json($transfer->toStringArray(), 200);
    }

    public function store(Request $request){
        $post = $request->all();
        try {
            $validator = Validator::make($post, [
                'BankTransfer' => 'required',

            ]);
            if($validator->fails()){
                throw new \Exception("Invalid Request");
            }
            $post = $post['BankTransfer'];

            $transfer = new BankTransfer($this->xero);
            $date = Carbon::createFromFormat("Y-m-d\TH:i:s", $post['Date']);

            if ( $date === false) {
                throw  new \Exception("Invalid Transfer Date format should be Y-m-d\TH:i:s");
            }
            $transfer->setDate($date);

            $fromBankAccount = new FromBankAccount($this->xero);
            $fromBankAccount->setCode($post['FromBankAccount']['Code']);
            $fromBankAccount->save();

          /*  $fromBankAccount = $this->xero->load("Accounting\\BankTransfer\\FromBankAccount")
                ->where('Code = "'.$post['FromBankAccount']['Code'].'" ')->execute()->first();*/

            $transfer->setFromBankAccount($fromBankAccount);

          /*  $toBankAccount = $this->xero->load("Accounting\\BankTransfer\\ToBankAccount")
                ->where('Code = "'.$post['ToBankAccount']['Code'].'" ')->execute()->first();*/

            $toBankAccount = new ToBankAccount($this->xero);
            $toBankAccount->setCode($post['ToBankAccount']['Code']);
            $toBankAccount->save();


            $transfer->setToBankAccount($toBankAccount);
            $transfer->setAmount($post['Amount']);
            $transfer = \simplexml_load_string( $transfer->save()->getResponseBody());


            return response()->json([
                'success'=> true,
                'message'=> config('api_response.xero.success_on_create'),
                'BankTransferID' => $transfer->BankTransfers->BankTransfer->BankTransferID
            ], 200);




       } catch( \Exception  $ex){

            return  response()->json(['success'=> 'false', 'message' => $ex->getMessage()], 500);
       }

    }



}
