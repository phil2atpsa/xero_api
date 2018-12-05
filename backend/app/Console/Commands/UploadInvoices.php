<?php
/**
 * Created by PhpStorm.
 * User: phil_
 * Date: 2018/12/05
 * Time: 14:58
 */

namespace App\Console\Commands;

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
    protected $signature = 'uploadInvoices';

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
        $spreadsheet = IOFactory::load(storage_path(config('export_templates.path.invoice')));
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
        }

    }

    private function process($data, $start, $end)
    {
        $app = new PrivateApplication(config('xero'));
        $invoice_service = new InvoiceService($app);


        for($i=$start;$i< $end; $i++) {
            $reference = $data[$i][2];
            $invoice_number = $data[$i][1]; //tid
            $reference = $data[$i][2]; //Reference
            $contact_name = $data[$i][3]; //Name
            $mobile = $data[$i][4]; //Cell
            $policy_number = $data[$i][5]; //policy Number
            $collection_amount = $data[$i][6]; // collection amount

            $contact_service = new ContactService($app);
            $contact = $contact_service->verifyContact($contact_name, $mobile);
           // $invoice_number = "echo190930";
            $existing = $app->load(InvoiceService::MODEL)->where('InvoiceNumber="' . $invoice_number . '"')
                ->execute()->first();


            if ($existing === null) {
                try {

                    $invoice = [
                        'Contact' => $contact,
                        'InvoiceNumber' => $invoice_number,
                        'Date' => \Carbon\Carbon::createFromFormat("Y-m-d", \Carbon\Carbon::now()->format('Y-m-d')),
                        'DueDate' => \Carbon\Carbon::createFromFormat("Y-m-d", \Carbon\Carbon::now()->format('Y-m-d')),
                        'Reference' => $reference,
                        'CurrencyCode' => 'ZAR',
                        'Status' => 'AUTHORISED',
                        'Type' => Invoice::INVOICE_TYPE_ACCREC,
                        'SubTotal' => $collection_amount,
                        'PolicyNumber' => $policy_number,
                        'LineAmountTypes' => 'Exclusive',
                        'TotalTax' => 0,
                        'Total' => $collection_amount,
                        'LineItems' => [[
                            'Description' => 'Unpaid premium for ' . $policy_number,
                            'Quantity' => 1,
                            'UnitAmount' => $collection_amount,
                            'TaxType' => 'OUTPUT',
                            'TaxAmount' => 0,
                            'LineAmount' => $collection_amount,
                            'AccountCode' => 200
                        ]]


                    ];
                    // array_push($invoices, $invoice);

                    echo $invoice_service->saveBulk($invoice)." Invoice {$invoice_number} Saved for Contact " . $contact->Name . "\n";

                } catch(\Exception $ex){
                    echo $ex->getMessage()."\n";
                    continue;
                }

            }

        }

    }

}