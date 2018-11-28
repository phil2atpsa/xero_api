import { Component } from '@angular/core';
import {ActivatedRoute, Router} from '@angular/router';
import {AuthService} from '../../services/auth.service';

@Component({
  selector: 'app-dashboard',
  templateUrl: 'register.component.html'
})
export class RegisterComponent {
  msg: any = {
    error : '',
    success : ''
  };
  model: any = {};
  loading = false;
  constructor(  private route: ActivatedRoute ,
                private router: Router,
                private authService: AuthService) { }
  create_account() {
    this.msg.error = '';
    this.loading = true;
    if (this.model.username == null) {
      this.msg.error = 'Please, specify a username';
      this.loading = false;
    } else if ( this.model.email == null) {
      this.msg.error = 'Please, specify an email';
      this.loading = false;
    }  else if (!this.validateEmail(this.model.email)) {
      this.msg.error = 'Invalid Email provided.';
      this.loading = false;
    } else if (this.model.password == null){
      this.msg.error = 'Please, specify an password.';
      this.loading = false;
    }  else if (this.model.repassword == null){
      this.msg.error = 'Please, retype a password.';
      this.loading = false;
    }  else if (this.model.repassword !== this.model.password ){
      this.msg.error = 'Password Mismatch.';
      this.loading = false;
    } else {
      this.authService.register(this.model.username, this.model.password, this.model.email)
        .then(res => {
          this.router.navigate(['/user_details']);
        }, rejected => {
          const error = rejected.error;
          let  error_message = '';
          if (error.message) {
            error_message += error.message + '\n';
          }
          const errors =  error.errors;
          if (errors.message) {
            error_message +=  errors.message + '\n';
          }


          const password_error_msg = errors.password;
          const username_error_msg = errors.username;

          if (username_error_msg) {
            error_message +=  username_error_msg[0] + '\n';
          }
          if (password_error_msg) {
            error_message += password_error_msg[0] + '\n';
          }
          this.msg.error = error_message;
          this.loading = false;

        });
    }

  }

  validateEmail(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
  }

}
