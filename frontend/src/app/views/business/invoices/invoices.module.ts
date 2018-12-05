import { NgModule } from '@angular/core';
import {CommonModule} from "@angular/common";
import {FormsModule, ReactiveFormsModule} from "@angular/forms";
import {InvoicesRoutingModule} from "./invoices-routing.module";
import {NgSelectModule} from "@ng-select/ng-select";
import {CollapseModule} from "ngx-bootstrap";
import {SharedModule} from "../../../shared/shared.module";
import {BusinessModule} from "../../business/business.module";
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

import {InvoicesComponent} from './invoices.component';
import {ViewInvoiceComponent} from './view-invoice/view-invoice.component';
import { FormInvoiceComponent } from './form-invoice/form-invoice.component';
import {PaymentFormComponent} from "../../accounting/payments/payment-form/payment-form.component";
import { ImportInvoiceComponent } from './import-invoice/import-invoice.component';
@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    InvoicesRoutingModule,
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
   InvoicesComponent,
   ViewInvoiceComponent,
   FormInvoiceComponent,
   ImportInvoiceComponent
  ],
  entryComponents: [
    PaymentFormComponent
  ]
})
export class InvoicesModule { }
