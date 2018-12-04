import { Component, OnInit, ViewChild } from '@angular/core';
import {PaymentService} from "../../../services/payment.service";
import {MatSort, MatTableDataSource,MatPaginator,MatDialog, MatDialogConfig} from "@angular/material";
import {PaymentFormComponent} from "./payment-form/payment-form.component";

@Component({
  selector: 'app-payments',
  templateUrl: './payments.component.html',
  styleUrls: ['./payments.component.scss']
})
export class PaymentsComponent implements OnInit {
  
  list: MatTableDataSource<any>;
  searchKey ='';
  matDialogConfig: MatDialogConfig = new MatDialogConfig();

  displayedColumns: string[] = [
    'InvoiceNumber',
    'Contact',
    'AccountCode',
    'Date',
    'Reference',
    'Status',
    'PaymentType',
    'Reconciled',
    'Amount'
    

  ];
  
  @ViewChild(MatSort) sort: MatSort;
  @ViewChild(MatPaginator) paginator: MatPaginator;
  
  constructor(private paymentService:PaymentService, private dialog: MatDialog) {
      this.matDialogConfig.disableClose = true;
      this.matDialogConfig.autoFocus = true;
      this.matDialogConfig.width = '55%';
    
  }

  ngOnInit() {
    this.refresh();
  }
  refresh() {
    this.paymentService.getList().subscribe(
      list => {
        const array = list.map(
          item => {
            // console.log(item);
            return {...item};
          });
          this.list = new MatTableDataSource(array);
        this.list.sort = this.sort;
        this.list.paginator = this.paginator;
      }
    );
  }

  onSearchClear() {
    this.searchKey = '';
    this.applyFilter();
  }
  applyFilter() {
    this.list.filter = this.searchKey.trim().toLowerCase();

  }
  
  make_payment(){
      this.dialog.open(PaymentFormComponent)
      .afterClosed()
      .subscribe(res => {
        this.refresh();
      });
       
      
  }

}
