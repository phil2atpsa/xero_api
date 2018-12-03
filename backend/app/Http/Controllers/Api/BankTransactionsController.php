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
use App\Services\TransactionService;

class BankTransactionsController extends Controller {

    protected $xero;
    private $transaction_service;

    static MODEL = "Accounting\\BankTransaction";

    public function __construct(XeroService $xeroService) {
        $this->xero = $xeroService;
        $this->transaction_service = new TransactionService($this->xero->getApplication());
    }

    public function index() {
        $bankTransactions = $this->xero->load('Accounting\\BankTransaction')->execute();
        return response()->json($bankTransactions->getArrayCopy(), 200);
    }

    public function show(string $id) {
        $bankTransaction = $this->xero->loadByGUID('Accounting\\BankTransaction', $id);
        return response()->json($bankTransaction->toStringArray(), 200);
    }

    public function store(Request $request) {
        $post = $request->all();

        try {
            $validator = Validator::make($post, [
                        'BankTransaction' => 'required',
            ]);



            if ($validator->fails()) {
                throw new \Exception("Invalid Request");
            }

            $post = $post['BankTransaction'];


            return response()->json([
            'success' => true,
            'message' => config('api_response.xero.success_on_create'),
            'BankTransferID' => $this->transaction_service->create($post);
            ], 200);
        } catch (\Exception $ex) {

            return response()->json(['success' => 'false', 'message' => $ex->getMessage()], 500);
        }
    }

    public function update(Request $request, string $id) {
        $post = $request->all();

        try {
            $validator = Validator::make($post, [
                        'BankTransaction' => 'required',
            ]);



            if ($validator->fails()) {
                throw new \Exception("Invalid Request");
            }

            $post = $post['BankTransaction'];




            return response()->json([
                        'success' => true,
                        'message' => config('api_response.xero.success_on_create'),
                        'BankTransferID' => $this->transaction_service->create($post, $id)
                            ], 200);
        } catch (\Exception $ex) {

            return response()->json(['success' => 'false', 'message' => $ex->getMessage()], 500);
        }
    }

}
