import { Component, OnInit } from '@angular/core';
import {StoresService} from '../../../services/stores.service';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import {FileUploadService} from '../../../services/file-upload.service';
import {AuthService} from '../../../services/auth.service';
import {MatDialogRef} from '@angular/material';
import {StoreGroupService} from '../../../services/store-group.service';
import {environment} from '../../../../environments/environment';
import * as _ from  'lodash';
import {StoreGroup} from '../../../models/store-group.model';
import {CustomValidator} from '../../../shared/custom_validator';
import {api_path} from '../../../../environments/global';
import swal from "sweetalert2";

@Component({
  selector: 'app-store-group-form',
  templateUrl: './store-group-form.component.html',
  styleUrls: ['./store-group-form.component.scss']
})
export class StoreGroupFormComponent implements OnInit {

  submitted = false;
  isCollapsed = false;
  iconCollapse = 'icon-arrow-up';
  store_form: FormGroup;
  logo = '';
  selected_store: StoreGroup = null;

  private avatar: File = null;
  private location: any = {};

  constructor(private storeGroupService: StoreGroupService,
              private formBuilder: FormBuilder,
              private fileUpload: FileUploadService,
              private authService: AuthService,
              public matDialogRef: MatDialogRef<StoreGroupFormComponent>) { }

  ngOnInit() {
    this.selected_store = this.storeGroupService.getStoreGroup();
    this.store_form = this.formBuilder.group({
      name : ['', Validators.required],
      address : ['', Validators.required],
      avatar : ['', Validators.required],
      tel_number: ['', Validators.required],
      store_representatives_name: ['', Validators.required],
      store_representatives_surname: ['',  Validators.required],
      store_representatives_email: ['', [Validators.required, Validators.email, CustomValidator.emailValidator] ],

    });
    if (this.selected_store  != null) {
      this.logo = environment.stores_image_path + '' + this.selected_store.avatar;
      this.selected_store.avatar = null;
      this.store_form.setValue(_.omit(this.selected_store, ['id', 'operating_times_weekday_from',
        'operating_times_weekday_to', 'operating_times_saturday_from', 'operating_times_saturday_to',
        'operating_times_sunday_from', 'operating_times_sunday_to', 'operating_times_ph_from',
        'operating_times_ph_to', 'store_representatives_cell_no']));
    }
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
    this.storeGroupService.initializeStore();
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
        'tel_number': this.store_form.value.tel_number,
        'user_id':  this.authService.getCurrentUser().id,
        'parent_id' :  0,
        'is_unique' :  0,
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
      }
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
