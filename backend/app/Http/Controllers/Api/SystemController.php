<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class SystemController extends Controller
{
    //
    public function check_invoices()
    {
        try {
            call_in_background("UploadInvoices");

            $process = new Process("ps -aux | grep artisan");
            // executes after the command finishes
            $process->run();
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }


            if(preg_match('/php artisan UploadInvoices/', $process->getOutput())) {
                return  response()->json(['success'=>true, 'message'=> 'UploadInvoices Running' ], 200);
            } else {
                return  response()->json(['success'=>false, 'message'=> 'UploadInvoices Not Running' ], 200);
            }

        } catch( ProcessFailedException $ex){
           return  response()->json(['success'=>false, 'message'=>$ex->getMessage()], 500);
        }

    }
}
