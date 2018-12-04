<?php
/**
 * Created by PhpStorm.
 * User: phil_
 * Date: 2018/12/03
 * Time: 19:30
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use App\Services\XeroService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * @var XeroService
     */
    protected $xero;
    /**
     * @var PaymentService
     */
    private $payment_service;

    /**
     * PaymentController constructor.
     * @param XeroService $xeroService
     */
    public function __construct(XeroService $xeroService) {
        $this->xero = $xeroService;
        $this->payment_service = new PaymentService($this->xero->getApplication());
    }

    public function index()
    {
        $payments = $this->xero->load(PaymentService::MODEL)->execute();
        return response()->json($payments->getArrayCopy(), 200);
    }

    public function store(Request $request)
    {
        try {
            $post = $request->all();
            $post = $post['Payment'];
            return response()->json([
                'success'=> true,
                'message'=> config('api_response.xero.success_on_create'),
                'PaymentID' =>  $this->payment_service->makePayment($post)
            ], 200);


        } catch (\Exception $ex){
            return  response()->json(['success'=> 'false', 'message' => $ex->getMessage()], 500);
        }
    }
}