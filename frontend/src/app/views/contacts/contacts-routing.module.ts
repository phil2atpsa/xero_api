import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { ContactsComponent } from '../contacts/contacts.component';
import {FormComponent} from "./form/form.component";
import {ViewComponent} from "./view/view.component";

const routes: Routes = [
  {
    path: '',
    data: {
      title: 'Contacts'
    },
    children: [
      {
        path: '',
        component: ContactsComponent,
        data: {
          title: 'List'
        }
      },
      {
        path: 'edit',
        component: FormComponent,
        data: {
          title: 'Store'
        }
      },
      {
        path: 'view',
        component: ViewComponent,
        data: {
          title: 'View'
        }
      }
    ]
  },


];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class ContactsRoutingModule {}
