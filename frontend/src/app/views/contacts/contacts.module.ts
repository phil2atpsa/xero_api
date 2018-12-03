import { NgModule } from '@angular/core';
import {CommonModule} from "@angular/common";
import {FormsModule, ReactiveFormsModule} from "@angular/forms";
import {ContactsRoutingModule} from "./contacts-routing.module";
import {NgSelectModule} from "@ng-select/ng-select";
import {CollapseModule} from "ngx-bootstrap";
import {SharedModule} from "../../shared/shared.module";
import {
  MatCheckboxModule,
  MatDialogModule,
  MatIconModule,
  MatInputModule,
  MatPaginatorModule,
  MatProgressSpinnerModule,
  MatSortModule,
  MatTableModule
} from "@angular/material";
import {ContactsComponent} from "./contacts.component";
import { FormComponent } from './form/form.component';
import { ViewComponent } from './view/view.component';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    ContactsRoutingModule,
    MatInputModule,
    MatPaginatorModule,
    MatProgressSpinnerModule,
    MatSortModule,
    MatTableModule,
    MatIconModule,
    ReactiveFormsModule,
    MatDialogModule,
    CollapseModule.forRoot(),
    NgSelectModule,
    SharedModule,
    MatCheckboxModule,
  ],
  declarations: [
    ContactsComponent,
    FormComponent,
    ViewComponent
  ],
  entryComponents: [
    FormComponent
  ]
})
export class ContactsModule { }
