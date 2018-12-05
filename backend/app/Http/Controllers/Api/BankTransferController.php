<?php
/**
 * Created by PhpStorm.
 * User: phil_
 * Date: 2018/11/23
 * Time: 14:12
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Services\BankTransferService;
use App\Services\XeroService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use XeroPHP\Application\PrivateApplication;
use XeroPHP\Models\Accounting\BankTransfer;
use XeroPHP\Models\Accounting\BankTransfer\FromBankAccount;
use XeroPHP\Models\Accounting\BankTransfer\ToBankAccount;

class BankTransferController extends  Controller
{



    private $xero = null;
    private $bank_transfer_service;

    /**
     * BankTransferController constructor.
     * @param XeroService $xeroService
     */
    public function __construct(XeroService $xeroService)
    {
        $this->xero = $xeroService;
        $this->bank_transfer_service = new BankTransferService($this->xero->getApplication());
    }

    public function index(){
        $transfers = $this->xero->load(BankTransferService::MODEL)->execute();
        return response()->json($transfers->getArrayCopy(), 200);
    }
    public function show(string $id){
        $transfer = $this->xero->loadByGUID(BankTransferService::MODEL,$id );
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

            return response()->json([
                'success'=> true,
                'message'=> config('api_response.xero.success_on_create'),
                'BankTransferID' => $this->bank_transfer_service->create($post)
            ], 200);

       } catch( \Exception  $ex){

            return  response()->json(['success'=> 'false', 'message' => $ex->getMessage()], 500);
       }

    }



}
