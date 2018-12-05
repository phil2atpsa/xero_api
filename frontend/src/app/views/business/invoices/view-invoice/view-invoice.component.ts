import { Component, OnInit } from '@angular/core';
import {MatDialog, MatDialogConfig} from "@angular/material";
import {PaymentFormComponent} from "../../../accounting/payments/payment-form/payment-form.component";
import {InvoicesService} from "../../../../services/invoices.service";

@Component({
  selector: 'app-view-invoice',
  templateUrl: './view-invoice.component.html',
  styleUrls: ['./view-invoice.component.scss']
})
export class ViewInvoiceComponent implements OnInit {
  
  invoice : any;
  matDialogConfig: MatDialogConfig = new MatDialogConfig();

  constructor(private dialog: MatDialog, private invoiceService: InvoicesService) {
    this.matDialogConfig.disableClose = true;
    this.matDialogConfig.autoFocus = true;
    this.matDialogConfig.width = '55%';
  }

  ngOnInit() {
       if(localStorage.getItem("invoice") != null) {
        this.invoice = JSON.parse(localStorage.getItem("invoice"));
       }
  }

  make_payment(){
    this.dialog.open(PaymentFormComponent)
      .afterClosed()
      .subscribe(res => {
        this.refresh();
      });
  }

  refresh(){
    this.invoiceService.SingleInvoice(this.invoice.InvoiceID)
      .then(res => {
          this.invoice = res;
      });
  }

}
