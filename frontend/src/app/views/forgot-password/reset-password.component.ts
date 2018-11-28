import { Component, OnInit } from '@angular/core';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import {UserService} from '../../services/user.service';
import {Router} from '@angular/router';
@Component({
  selector: 'app-dashboard',
  templateUrl: './reset-password.component.html',
})
export class ResetPasswordComponent implements OnInit {

  msg: any = {
    error : '',
    success : ''
  };
  loading = false;
  password_form: FormGroup;
  submitted = false;
  constructor(private formBuilder: FormBuilder, private userService: UserService, private router: Router) { }

  ngOnInit() {
    this.password_form = this.formBuilder.group({
      password: ['', Validators.required],
      repassword: ['', Validators.required],

    });
  }
  reset_username() {
    this.loading = true;
    if (this.password_form.invalid) {
      /*this.msg.error = this.password_form.errors.toString();*/
      /*console.log( this.password_form.errors);*/
      this.loading = false;
      return;
    }
    if (this.password_form.value.password !== this.password_form.value.repassword ) {
      this.msg.error = 'Password Mismatch';
      this.loading = false;
    } else {
      this.userService.updatePassword( localStorage.getItem('rUser') , this.password_form.value.password)
        .then( res => {
          this.router.navigate(['/login']);
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
  }

  get f() { return this.password_form.controls; }

}
