import {Component, Input, OnInit} from '@angular/core';
import { navItems } from './../../_nav';
import {AuthService} from '../../services/auth.service';

@Component({
  selector: 'app-dashboard',
  templateUrl: './default-layout.component.html'
})
export class DefaultLayoutComponent implements  OnInit{
  public navItems = navItems;
  public sidebarMinimized = true;
  private changes: MutationObserver;
  public element: HTMLElement = document.body;
  constructor(private auth: AuthService) {
    this.changes = new MutationObserver((mutations) => {
      this.sidebarMinimized = document.body.classList.contains('sidebar-minimized');
    });

    this.changes.observe(<Element>this.element, {
      attributes: true
    });
  }

  ngOnInit(): void {
    const is_admin : boolean =  this.auth.getCurrentUser().permissions.indexOf('app_admin') != -1;
    if (!is_admin) { // Not Admin remove all admin menu
        this.navItems.splice(5, 1);
    }
  }
}
