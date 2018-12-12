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
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Payments as Unallocated;
use App\Collections;

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
     * @var array
     */
    private static $banks = [
        'FNB',
        'CAPITEC',
        'NEDBANK',
        'ABSA',
        'STANDARD BANK',
        'BIDVEST BANK',
        'INVESTEC LIMITED BANK'

    ];

    /**
     * @var array
     */
    private static $method = [
        'EFT',
        'CASH',
        'DEBIT',
        'CREDIT',

    ];

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
    
    public function make_kp_payment(Request $request)
    {

        $post = Input::all();
        
         try {
            $validator = Validator::make($post, [
                'policynumber' => 'required',
                'amount' => 'required',
                'bank'=> 'required',
                'method' => 'required',
                'ref' => 'required'

            ]);
            if($validator->fails()){
                throw new \Exception("Required Fields Missing : PolicyNumber|Amount|Ref|Bank|Method");
            }

            if(!in_array( strtoupper($post['bank']), static::$banks)){
                throw new \Exception("Bank should be one of the following :".implode("|", static::$banks));
            }

             if(!in_array( strtoupper($post['method']), static::$method)){
                 throw new \Exception("Payment method be one of the following :".implode("|", static::$method));
             }
            
              return response()->json([
                'success'=> true,
                'message'=> config('api_response.xero.success_on_create'),
                'PaymentID' =>  $this->payment_service->kp_payment($post['policynumber'], $post['amount'],
                    $post['ref'], $post['bank'], $post['method'])
            ], 200);

            
         } catch(\Exception $ex){
             return  response()->json(['success'=> 'false', 'message' => $ex->getMessage()], 500);
         }
         
        
    }

    public function unallocated_payments()
    {
        return response()->json(Unallocated::Where('processed',0)->get()->toArray(), 200 );

    }
    public function allocate_payment(int $id)
    {
        $unallocated_payment = Unallocated::find($id);
        try {
            if (!Collections::where('policy_number', $unallocated_payment->policy_number)->exists())
                throw  new \Exception('Invoice with reference number ' . $unallocated_payment->policy_number . '
                does not exist in the system. Please make sure it is uploaded and try again');

            return response()->json([
                'success' => true,
                'message' => config('api_response.xero.success_on_create'),
                'PaymentID' => $this->payment_service->kp_payment($unallocated_payment->policy_number, $unallocated_payment->amount,
                    $unallocated_payment->reference, $unallocated_payment->bank, $unallocated_payment->method, $unallocated_payment)
            ], 200);
        } catch(\Exception $ex){

            return  response()->json(['success'=> 'false', 'message' => $ex->getMessage()], 200);
        }




    }
}