<?php
/**
 * Created by PhpStorm.
 * User: phil_
 * Date: 2018/11/28
 * Time: 14:12
 */

namespace App\Services;


use XeroPHP\Application\PrivateApplication;
use XeroPHP\Models\Accounting\Address;
use XeroPHP\Models\Accounting\Contact;
use XeroPHP\Models\Accounting\Contact\ContactPerson;
use XeroPHP\Models\Accounting\Phone;

class ContactService
{
    const MODEL ='Accounting\\Contact';
    /**
     * @var Contact
     */
    private $contact;
    /**
     * @var PrivateApplication
     */
    private $xero;


    /**
     * ContactService constructor.
     * @param PrivateApplication $xero
     */
    public function __construct(PrivateApplication $xero)
    {
        $this->xero = $xero;
        $this->contact = new Contact( $this->xero);
    }

    /**
     * @param string $id
     * @return mixed|null|\XeroPHP\Remote\Model
     * @throws \XeroPHP\Exception
     * @throws \XeroPHP\Remote\Exception
     * @throws \XeroPHP\Remote\Exception\NotFoundException
     */
    public function search(string $id)
    {
        if(preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}/', $id)) {
            $contact = $this->xero->load(self::MODEL)
                ->where('EmailAddress = "'.$id.'"')
                ->execute()
                ->first();
        } else {
            $contact = $this->xero->loadByGUID(self::MODEL, $id);
        }
        return $contact;

    }
    public function verifyContact($name, $mobile)
    {
        $contact = $this->xero->load(self::MODEL)
            ->where('Name = "'.$name.'"')->execute()
            ->first();
        
        
        if($contact) {
           return $contact;
        } else {
            $post['Name'] = $name;
            $post['DefaultCurrency'] ='ZAR';
            $post['Phones'][] =['PhoneType' => 'MOBILE', 'PhoneNumber'=>$mobile];
            $contactID = $this->create($post);
            return $this->xero->loadByGUID(self::MODEL,$contactID[0] );
        }
    }

    /**
     * @param array $post
     * @param string|null $contactID
     * @return \SimpleXMLElement
     * @throws \Exception
     */
    public function create(array $post, string $contactID = null)
    {
        try {
           
            $this->contact->setName($post['Name']);

            if($contactID != null)
                $this->contact->setContactID($contactID);


            if (isset($post['ContactNumber']))
                $this->contact->setContactNumber($post['ContactNumber']);

            if (isset($post['ContactStatus']))
                $this->contact->setContactStatus($post['ContactStatus']);

            if (isset($post['DefaultCurrency']))
                $this->contact->setDefaultCurrency($post['DefaultCurrency']);

            if (isset($post['EmailAddress']))
                $this->contact->setEmailAddress($post['EmailAddress']);

            if (isset($post['BankAccountDetails']))
                $this->contact->setBankAccountDetail($post['BankAccountDetails']);

            if (isset($post['SkypeUserName']))
                $this->contact->setSkypeUserName($post['SkypeUserName']);

            if (isset($post['TaxNumber']))
                $this->contact->setTaxNumber($post['TaxNumber']);

            if (isset($post['AccountsReceivableTaxType']))
                $this->contact->setAccountsReceivableTaxType($post['AccountsReceivableTaxType']);

            if (isset($post['AccountsPayableTaxType']))
                $this->contact->setAccountsPayableTaxType($post['AccountsPayableTaxType']);

            if (isset($post['FirstName']))
                $this->contact->setFirstName($post['FirstName']);

            if (isset($post['LastName']))
                $this->contact->setLastName($post['LastName']);

            if (isset($post['Addresses'])) {
                foreach ($post['Addresses'] as $addresses) {
                    $address = new Address($this->xero);
                    if (isset($addresses['AddressType']))
                        $address->setAddressType($addresses['AddressType']);
                    if (isset($addresses['AttentionTo']))
                        $address->setAttentionTo($addresses['AttentionTo']);
                    if (isset($addresses['AddressLine1']))
                        $address->setAddressLine1($addresses['AddressLine1']);
                    
                     if (isset($addresses['AddressLine2']))
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
                    $this->contact->addAddress($address);

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
                    $this->contact->addPhone($phone);

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

                    $this->contact->addContactPerson($contact_person);

                }
            }
            $this->contact = \simplexml_load_string($this->contact->save()->getResponseBody());


           return  $this->contact->Contacts->Contact->ContactID;
           
        } catch(\Exception  $ex){

            throw new \Exception($ex->getMessage());
        }
        
    }

}