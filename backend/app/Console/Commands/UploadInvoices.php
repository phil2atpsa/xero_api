<?php
/**
 * Created by PhpStorm.
 * User: phil_
 * Date: 2018/12/05
 * Time: 14:58
 */

namespace App\Console\Commands;

use App\Collections;
use Complex\Exception;
use Illuminate\Console\Command;
use App\Services\ContactService;
use App\Services\InvoiceService;
use PhpOffice\PhpSpreadsheet\IOFactory;
use XeroPHP\Application\PrivateApplication;
use XeroPHP\Models\Accounting\Invoice;

class UploadInvoices  extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UploadInvoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Wil upload invoices onto the system';


    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function handle()
    {
       /* $spreadsheet = IOFactory::load(storage_path(config('export_templates.path.invoice')));
        $worksheet = $spreadsheet->getActiveSheet();
        $data = $worksheet->toArray();
        $max_rows = 10;
        $length = count($data);
        $cycles =  ceil ($length / $max_rows);

        //$start = 1;
        for( $k = 1; $k <= $cycles; $k++){
            $start = $k == 1 ? $k : ($k - 1) * $max_rows;
            $end =  $k == 1 ? $max_rows:  $k  * $max_rows;
            $this->process($data, $start, $end );
            sleep(60);
        }*/
        $collections = Collections::Where('synced', 0)->get();

        foreach ($collections as $collection)
        {
            try {
                $this->process($collection);
            } catch(\Exception $ex){
                continue;
            }
        }


    }

    private function process(Collections $collection)
    {
        $app = new PrivateApplication(config('xero'));
        $invoice_service = new InvoiceService($app);
        $invoice_service->sync($collection, true);
    }

}