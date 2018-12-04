<?php

/**
 * Created by PhpStorm.
 * User: phil_
 * Date: 2018/11/23
 * Time: 14:57
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Services\TransactionService;
use App\Services\XeroService;

class BankTransactionsController extends Controller {

    protected $xero;
    private $transaction_service;

   

    public function __construct(XeroService $xeroService) {
        $this->xero = $xeroService;
        $this->transaction_service = new TransactionService($this->xero->getApplication());
    }

    public function index() {
        $bankTransactions = $this->xero->load(TransactionService::MODEL)->execute();
        return response()->json($bankTransactions->getArrayCopy(), 200);
    }

    public function show(string $id) {
        $bankTransaction = $this->xero->loadByGUID(TransactionService::MODEL, $id);
        return response()->json($bankTransaction->toStringArray(), 200);
    }

   

}
