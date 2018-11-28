import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import {StoresComponent} from './stores.component';
import {StoreGroupComponent} from './store-group.component';

const routes: Routes = [
  {
    path: '',
    data: {
      title: 'Manage'
    },
    children: [
      {
        path: 'store-group',
        component: StoreGroupComponent,
        data: {
          title: 'Store Groups'
        }
      },
      {
        path: 'stores',
        component: StoresComponent,
        data: {
          title: 'Stores'
        }
      },
    ]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class ManageRoutingModule {}
