import { Component, OnInit } from '@angular/core';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import {UserService} from '../../services/user.service';
import {Router} from '@angular/router';


@Component({
  selector: 'app-dashboard',
  templateUrl: './forgot-password.component.html',
})
export class ForgotPasswordComponent implements OnInit {

  msg: any = {
    error : '',
    success : ''
  };
  loading = false;
  email_form: FormGroup;
  submitted = false;
  constructor(private formBuilder: FormBuilder, private userService: UserService, private router: Router) { }
  ngOnInit() {
    this.email_form = this.formBuilder.group({
      username: ['', Validators.required]
    });
  }
  search_username() {
    this.submitted = true;
    this.loading = true;
    // stop here if form is invalid
    if (this.email_form.invalid) {
      this.loading = false;
      return;
    }
    this.userService.searchByUsername(this.email_form.value.username)
      .then( res => {
        localStorage.setItem('rUser',  res.id);
        this.router.navigate(['/reset_password']);
      }, rejected => {
        this.loading = false;
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
  get f() { return this.email_form.controls; }
}
