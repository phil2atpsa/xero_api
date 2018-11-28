import { Component, OnInit } from '@angular/core';
import {Router} from '@angular/router';
import {AuthService} from '../../services/auth.service';

@Component({
  selector: 'app-logout.component',
  templateUrl: './logout.component.html',

})
export class LogoutComponent implements OnInit {

  constructor( private router: Router,
                private authService: AuthService) { }

  ngOnInit() {
    this.authService.logout();
    this.router.navigate(['/']);
  }

}
