import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import {InvoicesComponent} from './invoices.component';
import {ViewInvoiceComponent} from './view-invoice/view-invoice.component';
import { FormInvoiceComponent } from './form-invoice/form-invoice.component';
import {ImportInvoiceComponent} from "./import-invoice/import-invoice.component";





const routes: Routes = [
  {
    path: '',
    
    data: {
      title: 'Invoices'
    },
    children: [
      {
        path: '',
        component: InvoicesComponent,
        data: {
          title: ''
        }
      },
      {
       
        path: 'view',
        component: ViewInvoiceComponent,
        data: {
          title: 'View'
        }      
      },
       {
       
        path: 'create',
        component: FormInvoiceComponent,
        data: {
          title: 'New'
        }      
      },
      {

        path: 'import',
        component: ImportInvoiceComponent,
        data: {
          title: 'Import'
        }
      },
    ]
  },


];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class InvoicesRoutingModule {}
