import {Component, OnInit} from '@angular/core';
import { AuthService} from '../../services/auth.service';
import {ActivatedRoute, Router} from '@angular/router';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';



@Component({
  selector: 'app-dashboard',
  templateUrl: 'login.component.html'
})


export class LoginComponent implements OnInit {

  msg: any = {
    error : '',
    success : ''
  };
  loading = false;
  returnUrl: string;
  loginForm: FormGroup;
  submitted = false;

  constructor(
    private route: ActivatedRoute ,
    private router: Router,
    private authService: AuthService,
    private formBuilder: FormBuilder ) { }

  authenticate(): void {
    this.submitted = true;
    this.msg.error = '';
    this.loading = true;
    if (this.loginForm.invalid) {
      this.loading = false;

      return;
    }
    this.authService.login(this.loginForm.value.username, this.loginForm.value.password)
      .then(res => {
        this.router.navigate([this.returnUrl]);
      }, reject => {
        const error = reject.error;
        let error_message = '';
        if (error.message) {
          error_message += error.message + '\n';
        }
        const errors =  error.errors;
        if (errors.message) {
          error_message +=  errors.message;
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


  ngOnInit() {
    this.returnUrl = this.route.snapshot.queryParams['returnUrl'] || '/';
    this.loginForm = this.formBuilder.group({
      username: ['', Validators.required],
      password: ['', Validators.required && Validators.maxLength(6)],

    });
  }
  get f() { return this.loginForm.controls; }
}
