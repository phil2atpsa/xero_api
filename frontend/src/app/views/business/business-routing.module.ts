import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import {BusinessComponent} from './business.component';
import { CollectionsComponent } from './collections/collections.component';
import { UnallocatedComponent } from './unallocated/unallocated.component';



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
       {
        path: 'collections',
        component: CollectionsComponent,
        data: {
          title: 'Collections'
        }
      },
      {
        path: 'unallocated-payments',
        component: UnallocatedComponent,
        data: {
          title: 'Unallocated Payments'
        }
      },


      //UnallocatedComponent
      
    ]
  },


];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class BusinessRoutingModule {}
