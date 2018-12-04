import { NgModule } from '@angular/core';
import {CommonModule} from "@angular/common";
import {FormsModule, ReactiveFormsModule} from "@angular/forms";
import {NgSelectModule} from "@ng-select/ng-select";
import {CollapseModule} from "ngx-bootstrap";
import {SharedModule} from "../../shared/shared.module";
import {AccountingRoutingModule}  from "./accounting-routing.module";

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
import { AccountsComponent } from './accounts/accounts.component';
import { TransactionsComponent } from './transactions/transactions.component';
import { AccountingComponent } from './accounting.component';
import { AccountFormComponent } from './accounts/account-form/account-form.component';
import { PaymentsComponent } from './payments/payments.component';
import { PaymentFormComponent } from './payments/payment-form/payment-form.component';



@NgModule({
  imports: [
    CommonModule,
    FormsModule,
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
    AccountingRoutingModule
  ],
  declarations: [
    AccountsComponent,
    TransactionsComponent,
    AccountingComponent,
    AccountFormComponent,
    PaymentsComponent,
    PaymentFormComponent
  
  ],
  entryComponents: [
    AccountFormComponent,
    PaymentFormComponent
  ]
})
export class AccountingModule { }
