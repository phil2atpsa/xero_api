<?php
/**
 * Created by PhpStorm.
 * User: phil_
 * Date: 2018/11/22
 * Time: 12:59
 */

namespace App\Http\Controllers\Api;


use App\Services\ContactService;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use  App\Http\Controllers\Controller;
use App\Services\XeroService;


class ContactController extends Controller
{
    const CUSTOMER_STRING="IsCustomer = True";
    const SUPPLIER_STRING="IsSupplier = True";


    protected $xero;
    protected $contact_service;


    public function __construct(XeroService $xeroService)
    {
        $this->xero = $xeroService;
        $this->contact_service = new ContactService($this->xero->getApplication());

    }

    protected function getContactList(int $contactType) : array {
        $contacts = $this->xero->load(ContactService::MODEL);

        switch($contactType){
            case config('enums.contact_type.customer'):
                $contacts->where("".static::CUSTOMER_STRING."");
                break;
            case config('enums.contact_type.supplier'):
                $contacts->where("".static::SUPPLIER_STRING."");
                break;

        }
        $contacts = $contacts->execute();

        return $contacts->getArrayCopy();
    }

    public function index()
    {
        return response()->json($this->getContactList(config('enums.contact_type.all')), 200);

    }





    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request) :   \Illuminate\Http\JsonResponse
    {
        $post = $request->all();
       // print_r($post); exit;
        $post = $post['Contact'];

        try {
            return response()->json([
                'success'=> true,
                'message'=> config('api_response.xero.success_on_create'),
                'ContactID' =>  $this->contact_service->create($post)
            ], 200);


        } catch(\Exception $ex){
            return  response()->json(['success'=> 'false', 'message' => $ex->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $id)  :  \Illuminate\Http\JsonResponse
    {
        $post = $request->all();
        $post = $post['Contact'];
        $post = $request->all();
        $post = $post['Contact'];
        try {
            return response()->json([
                'success'=> true,
                'message'=> config('api_response.xero.success_on_create'),
                'ContactID' =>  $this->contact_service->create($post, $id)
            ], 200);


        } catch(\Exception $ex){
            return  response()->json(['success'=> 'false', 'message' => $ex->getMessage()], 500);
        }


    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id) : \Illuminate\Http\JsonResponse
    {


    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \XeroPHP\Exception
     * @throws \XeroPHP\Remote\Exception\NotFoundException
     */
    public function show($id) :  \Illuminate\Http\JsonResponse
    {

        $contact = $this->xero->loadByGUID(ContactService::MODEL, $id);

        return response()->json( $contact->toStringArray(), 200);

    }


    public function contact_infos()
    {
        try {
        $infos = [];

        $contacts = $this->xero->load('Accounting\\Contact');
        $contacts = $contacts->execute();
        $contacts = $contacts->getArrayCopy();

       // $infos['id'] =
        foreach($contacts as $contact){
            $info =[];
            $info['id'] = $contact['ContactID'];
            $info['name'] = $contact['Name'];
            $info['status'] = $contact['ContactStatus'];
            $info['email'] = $contact['EmailAddress'];
            $info['skype_user'] =  $contact['SkypeUserName'] ?? null;
            $info['iscustomer'] = $contact['IsCustomer'];
            $info['issupplier'] = $contact['IsSupplier'];


            foreach($contact['Addresses'] as $address) {
                if($address['AddressType'] == 'STREET'){
                    $info['city'] = $address['City'] ??  $address['City'];
                    $info['region'] = $address['Region'] ??  $address['Region'];
                    $info['postal_code'] = $address['PostalCode'] ??  $address['PostalCode'];
                    $info['country'] = $address['Country'] ??  $address['Country'];
                }
            }
            foreach($contact['Phones'] as $phone) {
                if($phone['PhoneType'] == 'MOBILE'){
                    $info['mobile_number'] =  $phone['PhoneCountryCode'].$phone['PhoneAreaCode'].$phone['PhoneNumber'];
                }
            }


            array_push($infos, $info);
        }

        array_walk($infos, [$this, 'pushInvoice']);


        return response()->json( $infos, 200);
        } catch(\Exception $ex){
            return response()->json(['success'=> 'false', 'message' => $ex->getMessage()], 500);
        }
    }

    private function  pushInvoice(&$info, $key){
        $invoice_service = new InvoiceService($this->xero->getApplication());
       // $info['payable'] = $invoice_service->getAmountPayableTo($info['id']);
       // $info['due'] = $invoice_service->getAmountDueBy($info['id']);
    }









}
