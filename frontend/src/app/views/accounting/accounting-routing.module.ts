import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { AccountsComponent } from './accounts/accounts.component';
import { TransactionsComponent } from './transactions/transactions.component';
import { AccountingComponent } from './accounting.component';
import { PaymentsComponent } from './payments/payments.component';

const routes: Routes = [
  {
    path: '',
    data: {
      title: 'Accounting'
    },
    children: [
      {
        path: '',
        component: AccountingComponent,
        data: {
          title: ''
        }
      },
        {
        path: 'payments',
        component: PaymentsComponent,
        data: {
          title: 'Payments'
        }
      },
      {
        path: 'accounts',
        component: AccountsComponent,
        data: {
          title: 'Accounts'
        }
      },
      {
        path: 'transactions',
        component: TransactionsComponent,
        data: {
          title: 'Transactions'
        }
      },
    ]
  },


];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class AccountingRoutingModule {}
