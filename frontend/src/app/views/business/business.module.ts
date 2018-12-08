import { NgModule } from '@angular/core';
import {CommonModule} from '@angular/common';
import {FormsModule, ReactiveFormsModule} from '@angular/forms';
import {BusinessRoutingModule} from './business-routing.module';
import {NgSelectModule} from '@ng-select/ng-select';
import {CollapseModule} from 'ngx-bootstrap';
import {SharedModule} from '../../shared/shared.module';
import {
  MatCheckboxModule,
  MatDialogModule,
  MatIconModule,
  MatInputModule,
  MatPaginatorModule,
  MatProgressSpinnerModule,
  MatSortModule,
  MatTableModule,
  MatProgressBarModule
} from '@angular/material';


import {BusinessComponent} from './business.component';
import { CollectionsComponent } from './collections/collections.component';
import { UnallocatedComponent } from './unallocated/unallocated.component';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    BusinessRoutingModule,
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
    MatProgressBarModule
  ],
  declarations: [
   BusinessComponent,
   CollectionsComponent,
   UnallocatedComponent,

  ]
})
export class BusinessModule { }
