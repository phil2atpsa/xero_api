<?php
/**
 * Created by PhpStorm.
 * User: phil_
 * Date: 2018/11/22
 * Time: 12:59
 */

namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use XeroPHP\Application\PrivateApplication;
use  App\Http\Controllers\Controller;
use XeroPHP\Models\Accounting\Contact;
use XeroPHP\Models\Accounting\Address;
use XeroPHP\Models\Accounting\Phone;
use XeroPHP\Models\Accounting\Contact\ContactPerson;
use XeroPHP\Remote\Exception\BadRequestException;


class ContactController extends Controller
{
    const CUSTOMER_STRING="IsCustomer = True";
    const SUPPLIER_STRING="IsSupplier = True";


    protected $xero;


    public function __construct()
    {
        $this->xero = new PrivateApplication(config('xero'));
    }

    protected function getContactList(int $contactType) : array {
        $contacts = $this->xero->load('Accounting\\Contact');

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
        $post = $post['Contact'];
        try {
            $contact = new Contact($this->xero);
            $contact->setName($post['Name']);

            if (isset($post['ContactNumber']))
                $contact->setContactNumber($post['ContactNumber']);

            if (isset($post['ContactStatus']))
                $contact->setContactStatus($post['ContactStatus']);

            if (isset($post['DefaultCurrency']))
                $contact->setDefaultCurrency($post['DefaultCurrency']);

            if (isset($post['EmailAddress']))
                $contact->setEmailAddress($post['EmailAddress']);

            if (isset($post['BankAccountDetails']))
                $contact->setBankAccountDetail($post['BankAccountDetails']);

            if (isset($post['SkypeUserName']))
                $contact->setSkypeUserName($post['SkypeUserName']);

            if (isset($post['TaxNumber']))
                $contact->setTaxNumber($post['TaxNumber']);

            if (isset($post['AccountsReceivableTaxType']))
                $contact->setAccountsReceivableTaxType($post['AccountsReceivableTaxType']);

            if (isset($post['AccountsPayableTaxType']))
                $contact->setAccountsPayableTaxType($post['AccountsPayableTaxType']);

            if (isset($post['FirstName']))
                $contact->setFirstName($post['FirstName']);

            if (isset($post['LastName']))
                $contact->setLastName($post['LastName']);

            if (isset($post['Addresses'])) {
                foreach ($post['Addresses'] as $addresses) {
                    $address = new Address($this->xero);
                    if (isset($addresses['AddressType']))
                        $address->setAddressType($addresses['AddressType']);
                    if (isset($addresses['AttentionTo']))
                        $address->setAttentionTo($addresses['AttentionTo']);
                    if (isset($addresses['AddressLine1']))
                        $address->setAddressLine2($addresses['AddressLine2']);
                    if (isset($addresses['AddressLine3']))
                        $address->setAddressLine3($addresses['AddressLine3']);

                    if (isset($addresses['AddressLine4']))
                        $address->setAddressLine4($addresses['AddressLine4']);

                    if (isset($addresses['City']))
                        $address->setCity($addresses['City']);

                    if (isset($addresses['Region']))
                        $address->setRegion($addresses['Region']);

                    if (isset($addresses['PostalCode']))
                        $address->setPostalCode($addresses['PostalCode']);

                    $address->save();
                    $contact->addAddress($address);

                }
            }

            if (isset($post['Phones'])) {
                foreach ($post['Phones'] as $phones) {
                    $phone = new Phone($this->xero);
                    if (isset($phones['PhoneType']))
                        $phone->setPhoneType($phones['PhoneType']);
                    if (isset($phones['PhoneNumber']))
                        $phone->setPhoneNumber($phones['PhoneNumber']);

                    if (isset($phones['PhoneAreaCode']))
                        $phone->setPhoneAreaCode($phones['PhoneAreaCode']);

                    if (isset($phones['PhoneCountryCode']))
                        $phone->setPhoneCountryCode($phones['PhoneCountryCode']);

                    $phone->save();
                    $contact->addPhone($phone);

                }
            }


             if (isset($post['ContactPersons'])) {
                $contactPerson = $post['ContactPersons']['ContactPerson'] ?? $post['ContactPersons']['ContactPerson'];
                if( $contactPerson) {
                    $contact_person = new ContactPerson($this->xero);

                    if(isset($contactPerson['FirstName']))
                        $contact_person->setFirstName($contactPerson['FirstName']);
                    if(isset($contactPerson['LastName']))
                        $contact_person->setLastName($contactPerson['LastName']);
                    if(isset($contactPerson['EmailAddress']))
                        $contact_person->setEmailAddress($contactPerson['EmailAddress']);

                    if(isset($contactPerson['IncludeInEmails']))
                        $contact_person->setIncludeInEmail($contactPerson['IncludeInEmails']);

                    $contact_person->save();

                    $contact->addContactPerson($contact_person);

                }
             }


             $contact = \simplexml_load_string($contact->save()->getResponseBody());

            return response()->json([
                    'success'=> true,
                    'message'=> config('api_response.xero.success_on_create'),
                    'ContactID' => $contact->Contacts->Contact->ContactID
            ], 200);
        } catch(\Exception  $ex){

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
        try {
            $contact = new Contact($this->xero);
            $contact->setName($post['Name']);
            $contact->setContactID($id);

            if (isset($post['ContactNumber']))
                $contact->setContactNumber($post['ContactNumber']);

            if (isset($post['ContactStatus']))
                $contact->setContactStatus($post['ContactStatus']);

            if (isset($post['DefaultCurrency']))
                $contact->setDefaultCurrency($post['DefaultCurrency']);

            if (isset($post['EmailAddress']))
                $contact->setEmailAddress($post['EmailAddress']);

            if (isset($post['BankAccountDetails']))
                $contact->setBankAccountDetail($post['BankAccountDetails']);

            if (isset($post['SkypeUserName']))
                $contact->setSkypeUserName($post['SkypeUserName']);

            if (isset($post['TaxNumber']))
                $contact->setTaxNumber($post['TaxNumber']);

            if (isset($post['AccountsReceivableTaxType']))
                $contact->setAccountsReceivableTaxType($post['AccountsReceivableTaxType']);

            if (isset($post['AccountsPayableTaxType']))
                $contact->setAccountsPayableTaxType($post['AccountsPayableTaxType']);

            if (isset($post['FirstName']))
                $contact->setFirstName($post['FirstName']);

            if (isset($post['LastName']))
                $contact->setLastName($post['LastName']);

            if (isset($post['Addresses'])) {
                foreach ($post['Addresses'] as $addresses) {
                    $address = new Address($this->xero);
                    if (isset($addresses['AddressType']))
                        $address->setAddressType($addresses['AddressType']);
                    if (isset($addresses['AttentionTo']))
                        $address->setAttentionTo($addresses['AttentionTo']);
                    if (isset($addresses['AddressLine1']))
                        $address->setAddressLine2($addresses['AddressLine2']);
                    if (isset($addresses['AddressLine3']))
                        $address->setAddressLine3($addresses['AddressLine3']);

                    if (isset($addresses['AddressLine4']))
                        $address->setAddressLine4($addresses['AddressLine4']);

                    if (isset($addresses['City']))
                        $address->setCity($addresses['City']);

                    if (isset($addresses['Region']))
                        $address->setRegion($addresses['Region']);

                    if (isset($addresses['PostalCode']))
                        $address->setPostalCode($addresses['PostalCode']);

                    $address->save();
                    $contact->addAddress($address);

                }
            }

            if (isset($post['Phones'])) {
                foreach ($post['Phones'] as $phones) {
                    $phone = new Phone($this->xero);
                    if (isset($phones['PhoneType']))
                        $phone->setPhoneType($phones['PhoneType']);
                    if (isset($phones['PhoneNumber']))
                        $phone->setPhoneNumber($phones['PhoneNumber']);

                    if (isset($phones['PhoneAreaCode']))
                        $phone->setPhoneAreaCode($phones['PhoneAreaCode']);

                    if (isset($phones['PhoneCountryCode']))
                        $phone->setPhoneCountryCode($phones['PhoneCountryCode']);

                    $phone->save();
                    $contact->addPhone($phone);

                }
            }


            if (isset($post['ContactPersons'])) {
                $contactPerson = $post['ContactPersons']['ContactPerson'] ?? $post['ContactPersons']['ContactPerson'];
                if( $contactPerson) {
                    $contact_person = new ContactPerson($this->xero);

                    if(isset($contactPerson['FirstName']))
                        $contact_person->setFirstName($contactPerson['FirstName']);
                    if(isset($contactPerson['LastName']))
                        $contact_person->setLastName($contactPerson['LastName']);
                    if(isset($contactPerson['EmailAddress']))
                        $contact_person->setEmailAddress($contactPerson['EmailAddress']);

                    if(isset($contactPerson['IncludeInEmails']))
                        $contact_person->setIncludeInEmail($contactPerson['IncludeInEmails']);

                    $contact_person->save();

                    $contact->addContactPerson($contact_person);

                }
            }


            $contact = \simplexml_load_string($contact->save()->getResponseBody());

            return response()->json([
                'success'=> true,
                'message'=> config('api_response.xero.success_on_update'),
                'ContactID' => $contact->Contacts->Contact->ContactID
            ], 200);
        } catch( \Exception  $ex){

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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return  \Illuminate\Http\JsonResponse
     */
    public function show($id) :  \Illuminate\Http\JsonResponse
    {

        $contact = $this->xero->loadByGUID("Accounting\\Contact", $id);

        return response()->json( $contact->toStringArray(), 200);

    }






}
