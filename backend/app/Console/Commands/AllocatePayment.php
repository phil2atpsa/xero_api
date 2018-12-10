<?php
/**
 * Created by PhpStorm.
 * User: phil_
 * Date: 2018/12/05
 * Time: 14:58
 */

namespace App\Console\Commands;


use Illuminate\Console\Command;
use App\Services\PaymentService;
use XeroPHP\Application\PrivateApplication;
use App\Payments;

class AllocatePayment  extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AllocatePayments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Wil Allocated payment to relevant invoices';


    /**
     *
     */
    public function handle()
    {
        foreach (Payments::all() as $payment)
        {
            try {
                $this->allocate($payment);
            } catch(\Exception $ex){
                continue;
            }
        }


    }

    private function allocate(Payments $payment)
    {
        $app = new PrivateApplication(config('xero'));
        $payment_service = new PaymentService($app);
        $payment_service->kp_payment($payment->policy_number,$payment->amount,
            $payment->reference,$payment->bank,$payment->method );
    }

}