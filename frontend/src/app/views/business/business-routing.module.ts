import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import {BusinessComponent} from './business.component';




const routes: Routes = [
  {
    path: '',
    
    data: {
      title: 'Business'
    },
    children: [
      {
        path: '',
        component: BusinessComponent,
        data: {
          title: ''
        }
      },
      {
       
        path: 'invoices',
        loadChildren: './invoices/invoices.module#InvoicesModule',
             
      },
    ]
  },


];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class BusinessRoutingModule {}
