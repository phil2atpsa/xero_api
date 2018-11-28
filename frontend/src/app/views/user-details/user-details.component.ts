import { Component, OnInit } from '@angular/core';
import {ActivatedRoute, Router} from '@angular/router';
import {CategoryService} from '../../services/category.service';
import {FormArray, FormBuilder, FormGroup, Validators} from '@angular/forms';
import {AuthService} from '../../services/auth.service';
import {UserModel} from '../../models/user.model';
import {FileUploadService} from '../../services/file-upload.service';
import {api_path} from '../../../environments/global';


@Component({
  selector: 'app-dashboard',
  templateUrl: './user-details.component.html',
})
export class UserDetailsComponent implements OnInit {
  msg: any = {
    error : '',
    success : ''
  };
  detailsForm: FormGroup;
  loading = false;
  submitted = false;
  business_category = [];

  private currentUser: UserModel;
  private user_key = 'user';
  private selected_file: File = null;
  private location: any = {};
  private post_url: string = api_path + 'user/complete_profile';

  constructor( private route: ActivatedRoute ,
               private router: Router,
               private categoryService: CategoryService,
               private authService: AuthService,
               private formBuilder: FormBuilder,
               private fileUpload: FileUploadService) {
  }

  fileUploaded(event) {
   // console.log(event);
    this.selected_file = <File> event.target.files[0];
  }
  complete_profile() {
    this.submitted = true;
    // stop here if form is invalid
    if (this.detailsForm.invalid) {
      this.loading = false;

      return;
    }
    const business_type_name: string[] = [];
    const business_type_id: number[] = [];
    this.detailsForm.value.business_type.map(
      (v, k) => {
        console.log(v);
        if (v) {
          business_type_id.push(this.business_category[k].id);
          business_type_name.push(this.business_category[k].name);
        }
      }
    );

    const post_data = {
      'id' :  this.authService.getCurrentUser().id,
      'name' : this.detailsForm.value.name,
      'surname' : this.detailsForm.value.surname,
      'mobile' : this.detailsForm.value.mobile,
      'dob' : this.detailsForm.value.dob,
      'gender' : this.detailsForm.value.gender,
      'email' : this.authService.getCurrentUser().email,
      'address': this.location.formatted_address,
      'geometry': this.location.geometry,
      'suburb' :  this.location.address_components[2].long_name,
      'city' : this.location.address_components[3].long_name,
      'province' :  this.location.address_components[5].long_name,
      'post_code' :  this.location.address_components[7].long_name,
      'business_category' :  business_type_id.join(','),
      'business': {
        'name' :  this.detailsForm.value.business_name,
        'type': business_type_name.join(',')
      },
    };

    this.fileUpload.uploadFileToUrl(this.post_url, this.selected_file, 'user_details', JSON.stringify(post_data), 'logo_file')
      .then( res => {
      this.currentUser = new UserModel().deserialize(res);
      localStorage.setItem(this.user_key, JSON.stringify(this.currentUser ));
      this.router.navigate(['/dashboard']);
    }, rejected => {
        const error = rejected.error;
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
        }
      this.msg.error = error_message;
      this.loading = false;
      });
  }
  getAddressOnChange(event) {
    this.location = event;
  }

  ngOnInit() {

    this.detailsForm = this.formBuilder.group({
      name: ['', Validators.required],
      surname: ['', Validators.required],
      gender: ['', Validators.required],
      address: [],
      dob: [],
      mobile: [],
      business_name: [],
      business_type: this.formBuilder.array([]),
      logo_file: ['', Validators.required]
    });

    this.categoryService.getParents()
      .then(res => {
        this.business_category = res;
        const bt: FormArray = this.detailsForm.get('business_type') as FormArray;
        this.business_category.map(c => {
         // alert(c.id);
          bt.push(this.formBuilder.control(false));
        });
      });

  }
  get business_type() {
    return this.detailsForm.get('business_type');
  }
  get f() { return this.detailsForm.controls; }

}
