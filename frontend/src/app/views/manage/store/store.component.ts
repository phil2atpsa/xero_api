import { Component, OnInit } from '@angular/core';
import {StoresService} from '../../../services/stores.service';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import {FileUploadService} from '../../../services/file-upload.service';
import {Store} from '../../../models/store.model';
import {MatDialogRef} from '@angular/material';

import * as _ from  'lodash';
import { CustomValidator } from '../../../shared/custom_validator';
import {AuthService} from '../../../services/auth.service';

import swal from 'sweetalert2';
import {environment} from '../../../../environments/environment';
import {api_path} from '../../../../environments/global';


@Component({
  selector: 'app-store',
  templateUrl: './store.component.html',
  styleUrls: ['./store.component.scss']
})
export class StoreComponent implements OnInit {

  constructor(private storeService: StoresService,
              private formBuilder: FormBuilder,
              private fileUpload: FileUploadService,
              private authService: AuthService,
              public matDialogRef: MatDialogRef<StoreComponent>) { }

  submitted = false;
  isCollapsed = false;
  iconCollapse = 'icon-arrow-up';
  storeGroups: Store[];
  store_form: FormGroup;
  logo = '';
  selected_store: Store = null;

  private avatar: File = null;
  private location: any = {};

  ngOnInit() {
     this.storeService.getParentStore().subscribe(
      list => {
         this.storeGroups = list.map(item => {
           return {...item};
         });

         this.selected_store = this.storeService.getStore();
         if (this.selected_store  != null) {
          this.store_form.setValue(_.omit(this.selected_store, ['id', 'business_id', 'lat', 'lng', 'area_code',
            'province', 'city', 'suburb', 'avatar', 'is_unique', 'created_at',  'updated_at']));
          this.logo = environment.stores_image_path + '' + this.selected_store.avatar;
         }

      });

     this.store_form = this.formBuilder.group({
       parent_id: ['', Validators.required],
       name : ['', Validators.required],
       address : ['', Validators.required],
       shopping_center: [],
       tel_number: ['', Validators.required],
       shop_number: [],
       store_representatives_name: ['', Validators.required],
       store_representatives_surname: ['',  Validators.required],
       store_representatives_email: ['', [Validators.required, Validators.email, CustomValidator.emailValidator] ],
       store_representatives_cell_no: ['',  Validators.required],
       operating_times_weekday_from: ['',  Validators.required],
       operating_times_weekday_to: ['',  Validators.required],
       operating_times_saturday_from: ['',  Validators.required],
       operating_times_saturday_to: ['',  Validators.required],
       operating_times_sunday_from: ['',  Validators.required],
       operating_times_sunday_to: ['',  Validators.required],
       operating_times_ph_from: ['',  Validators.required],
       operating_times_ph_to: ['',  Validators.required],

    });
  }

  collapsed(event: any): void {
    // console.log(event);
  }

  expanded(event: any): void {
    // console.log(event);
  }

  toggleCollapse(): void {
    this.isCollapsed = !this.isCollapsed;
    this.iconCollapse = this.isCollapsed ? 'icon-arrow-down' : 'icon-arrow-up';
  }

  getAddressOnChange(event) {
    this.location = event;
  }

  fileUploaded(event) {
    this.avatar = <File> event.target.files[0];
  }

  onCloseOperation() {
    this.matDialogRef.close();
    this.storeService.initializeStore();
    this.logo = '';
    this.store_form.reset();
  }

  store_infos() {
    this.submitted = true;
    const post_data = {
        'store': {
          'id' : this.selected_store != null ? this.selected_store.id : 0,
          'name': this.store_form.value.name,
          'geometry':  this.location.geometry,
          'shopping_center': this.store_form.value.shopping_center,
          'shop_number': this.store_form.value.shop_number,
          'tel_number': this.store_form.value.tel_number,
          'user_id':  this.authService.getCurrentUser().id,
          'parent_id' :  this.store_form.value.parent_id,
          'address' : this.location && this.location.formatted_address ?
            this.location.formatted_address : null,
          'suburb' :  this.location != null && typeof (this.location.address_components) !== 'undefined' ?
            this.location.address_components[2].long_name : null,
          'city' : this.location &&  typeof (this.location.address_components) !== 'undefined' ?
            this.location.address_components[3].long_name : null,
          'province' :  this.location &&  typeof (this.location.address_components) !== 'undefined'   ?
            this.location.address_components[5].long_name : null,
          'post_code' :   this.location &&  typeof (this.location.address_components) !== 'undefined'  ?
            this.location.address_components[7].long_name : null
        },
      'representatives' : {
          'name' : this.store_form.value.store_representatives_name,
          'email' : this.store_form.value.store_representatives_email,
          'surname': this.store_form.value.store_representatives_surname,
          'cell_no': this.store_form.value.store_representatives_cell_no,
      },
      'operating_times' : {
        'weekday_from' : this.store_form.value.operating_times_weekday_from,
        'weekday_to' : this.store_form.value.operating_times_weekday_to,
        'saturday_from': this.store_form.value.operating_times_saturday_from,
        'saturday_to': this.store_form.value.operating_times_saturday_to,
        'sunday_from': this.store_form.value.operating_times_sunday_from,
        'sunday_to': this.store_form.value.operating_times_sunday_to,
        'public_holiday_from': this.store_form.value.operating_times_ph_from,
        'public_holiday_to': this.store_form.value.operating_times_ph_to,
      },
    };

    this.fileUpload.uploadFileToUrl(
      api_path + 'user/stores',
      this.avatar, 'post_data', JSON.stringify(post_data), 'logo_file')
      .then(res => {
        swal('Success', 'Record Stored', 'success');
        this.onCloseOperation();
      }, reason => {
        const error = reason.error;
        let  error_message = '';
        if (error.message) {
          error_message += error.message + '\n';
        }
        const errors =  error.errors;
        if (errors  && errors.message) {
          error_message +=  errors.message + '\n';
        }
        if (errors) {
          const type_error_msg = errors.type;
          const name_error_msg = errors.name;
          const message_error_msg = errors.message;

          if (type_error_msg) {
            error_message += type_error_msg[0] + '\n';
          }
          if (name_error_msg) {
            error_message += name_error_msg[0] + '\n';
          }
          if (message_error_msg) {
            error_message += message_error_msg[0] + '\n';
          }
          swal('Error', error_message, 'error');
        }
      });
  }

  get f() { return this.store_form.controls; }

}
