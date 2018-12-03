import { Component, OnInit } from '@angular/core';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import {CustomValidator} from '../../../shared/custom_validator';
import {ContactService} from "../../../services/contact.service";
import swal from "sweetalert2";
import {Router} from "@angular/router";
import {Contact_formModel} from "../../../models/contact_form.model";
import * as _ from  'lodash';

@Component({
  selector: 'app-form',
  templateUrl: './form.component.html',
  styleUrls: ['./form.component.scss']
})
export class FormComponent implements OnInit {

  contact_form : FormGroup;
  submitted = false;
  loading = false;
  selected_contact: Contact_formModel = null;

  private location: any = {};

  constructor(
    private formBuilder: FormBuilder,
    private contactService: ContactService,
    private router: Router) { }

  ngOnInit() {
    this.contact_form = this.formBuilder.group({
      name : ['', Validators.required],
      contact_person_first_name:[],
      contact_person_last_name:[],
      phone_country_code:[],
      phone_area_code:[],
      phone_number:[],
      fax_country_code:[],
      fax_area_code:[],
      fax_number:[],
      mobile_country_code:[],
      mobile_area_code:[],
      mobile_number:[],
      skype_name:[],
      street_address:[],
      email: ['', [ Validators.required, Validators.email, CustomValidator.emailValidator] ],

    });

    if(localStorage.getItem("contact") != null) {
     // alert(localStorage.getItem("contact"));
      const contact = JSON.parse(localStorage.getItem("contact"));

      localStorage.removeItem("contact");
      this.selected_contact = new Contact_formModel();

      this.selected_contact.id = contact.ContactID;
      this.selected_contact.contact_person_first_name = contact.FirstName ? contact.FirstName :'' ;
      this.selected_contact.contact_person_last_name = contact.LastName ? contact.LastName : '';
      this.selected_contact.email = contact.EmailAddress ? contact.EmailAddress : '';
      const phones = contact.Phones;
      const addresses = contact.Addresses;
      phones.map(
        phone => {
         // alert(phone.PhoneType);

          if(phone.PhoneType == 'FAX'){
            this.selected_contact.fax_area_code = phone.PhoneAreaCode ? phone.PhoneAreaCode: '' ;
            this.selected_contact.fax_country_code = phone.PhoneCountryCode ? phone.PhoneCountryCode:'';
            this.selected_contact.fax_number = phone.PhoneNumber ?  phone.PhoneNumber: '';

          }
          if(phone.PhoneType == 'MOBILE'){
            this.selected_contact.mobile_area_code =  phone.PhoneAreaCode ? phone.PhoneAreaCode: '' ;
            this.selected_contact.mobile_country_code = phone.PhoneCountryCode ? phone.PhoneCountryCode:'';
            this.selected_contact.mobile_number = phone.PhoneNumber ?  phone.PhoneNumber: '';

          }
          if(phone.PhoneType == 'MOBILE'){
            this.selected_contact.mobile_area_code =phone.PhoneAreaCode ? phone.PhoneAreaCode: '' ;
            this.selected_contact.mobile_country_code = phone.PhoneCountryCode ? phone.PhoneCountryCode:'';
            this.selected_contact.mobile_number =phone.PhoneNumber ?  phone.PhoneNumber: '';

          }
          if(phone.PhoneType == 'DEFAULT'){
            this.selected_contact.phone_area_code =phone.PhoneAreaCode ? phone.PhoneAreaCode: '' ;
            this.selected_contact.phone_country_code = phone.PhoneCountryCode ? phone.PhoneCountryCode:'';
            this.selected_contact.phone_number =phone.PhoneNumber ?  phone.PhoneNumber: '';

          }
        }
      );


      this.selected_contact.name = contact.Name;
      this.selected_contact.skype_name = contact.SkypeUserName ?  contact.SkypeUserName : '';
      addresses.map(
        address => {
           if(address.AddressType == "STREET") {
             this.selected_contact.street_address = address.AddressLine1 ? address.AddressLine1  :'';
           }
        }
      )
      this.contact_form.setValue(_.omit(this.selected_contact, 'id'));
    }
  }
  getAddressOnChange(event) {
    this.location = event;
  }
  store_contact() {

    console.log(this.location);
    this.submitted = true;
    if (!this.contact_form.invalid) {

      this.loading = true;
      let post = {
        'Contact': {
          'Name': this.contact_form.value.name,
          'Status': 'ACTIVE',
          'EmailAddress': this.contact_form.value.email,
          'SkypeUserName': this.contact_form.value.skype_name,
          'FirstName': this.contact_form.value.contact_person_first_name,
          'LastName': this.contact_form.value.contact_person_last_name,
          'DefaultCurrency': 'ZAR',
          'Phones': [
            {
              'Type': 'DEFAULT',
              'PhoneNumber': this.contact_form.value.phone_number,
              'PhoneAreaCode': this.contact_form.value.phone_area_code,
              'PhoneCountryCode': this.contact_form.value.phone_country_code,

            },
            {
              'Type': 'FAX',
              'PhoneNumber': this.contact_form.value.fax_number,
              'PhoneAreaCode': this.contact_form.value.fax_area_code,
              'PhoneCountryCode': this.contact_form.value.fax_country_code,

            },
            {
              'Type': 'MOBILE',
              'PhoneNumber': this.contact_form.value.mobile_number,
              'PhoneAreaCode': this.contact_form.value.mobile_area_code,
              'PhoneCountryCode': this.contact_form.value.mobile_country_code,
            },

          ],
          'Addresses': []


        }
      }

      let addresses = [];
      const street_address = {
        'AddressType': 'STREET',
        'AttentionTo': this.contact_form.value.name,
        'AddressLine1':  this.location ? this.location.formatted_address: null,
        'City': '',
        'Region': '',
        'PostalCode': '',
        'Country': '',


      };
      if (this.location != null && this.location.address_components != null){
        const i  = this.location.address_components.length;
        street_address.PostalCode = this.location.address_components[i-1].long_name;
        street_address.Country = this.location.address_components[i-2].long_name;
        street_address.Region = this.location.address_components[i-3].long_name;
        street_address.City = this.location.address_components[i-4].long_name;

      }





      addresses.push(street_address);
      post.Contact.Addresses.push(addresses);

      let contactId = null;
      if(this.selected_contact != null)
        contactId = this.selected_contact.id;

      // console.log(this.location);
      this.contactService.saveContact(post, contactId)
        .then(res => {
          this.loading = false;
          this.submitted = false;

          swal('Success', 'Record Stored', 'success');
          this.reset();
          const $router = this.router;
          setTimeout(function () {
            $router.navigate(['/contacts']);
          }, 3000);


        }, rejected => {

         swal('Error', rejected.message, 'error');

          this.loading = false;
          this.submitted = false;


        });
    }
  }

  reset(){
    this.contact_form.value.name = '';
    this.contact_form.value.email ='';
    this.contact_form.value.skype_name='';
    this.contact_form.value.contact_person_first_name='';
    this.contact_form.value.contact_person_last_name='';
    this.contact_form.value.phone_number='';
    this.contact_form.value.phone_area_code='';
    this.contact_form.value.phone_country_code='';
    this.contact_form.value.fax_number='';
    this.contact_form.value.fax_area_code='';
    this.contact_form.value.fax_country_code='';
    this.contact_form.value.mobile_number='';
    this.contact_form.value.mobile_area_code='';
    this.contact_form.value.mobile_country_code='';
    this.contact_form.value.street_address='';
    this.location = null;
    this.selected_contact = null;

  }
  get f() { return this.contact_form.controls; }

}
