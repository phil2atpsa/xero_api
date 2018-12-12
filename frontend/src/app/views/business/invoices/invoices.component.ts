import { Component, OnInit, ViewChild } from '@angular/core';
import {InvoicesService} from "../../../services/invoices.service";
import {ContactService} from "../../../services/contact.service";
import {MatSort, MatTableDataSource,MatPaginator} from "@angular/material";
import {MatDialog, MatDialogConfig} from "@angular/material";
import {PaymentFormComponent} from "../../accounting/payments/payment-form/payment-form.component";
import {Router} from "@angular/router";
import swal from 'sweetalert2';

@Component({
  selector: 'app-invoices',
  templateUrl: './invoices.component.html',
  styleUrls: ['./invoices.component.scss']
})
export class InvoicesComponent implements OnInit {
  list: MatTableDataSource<any>;
  searchKey ='';
 // c_all = false;

  matDialogConfig: MatDialogConfig = new MatDialogConfig();
  displayedColumns: string[] = [
    'contact',
    'invoice_number',
    'reference',
    'date',
    'duedate',
    'total',
    'status',
   // 'invoice_actions'
  ];

  @ViewChild(MatSort) sort: MatSort;
  @ViewChild(MatPaginator) paginator: MatPaginator;   
  constructor(private invoiceService: InvoicesService,private contactService: ContactService,
              private router: Router, private dialog: MatDialog) {
    this.matDialogConfig.disableClose = true;
    this.matDialogConfig.autoFocus = true;
    this.matDialogConfig.width = '55%';
  }

  ngOnInit() {
      this.refresh();
  }
  
  refresh(){
      this.invoiceService.getAllInvoices().subscribe(
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
  onContactView(contactID: string){
        this.contactService.getSingleContact(contactID).then(
        res => {
          localStorage.setItem('contact', JSON.stringify(res));
          this.router.navigateByUrl("contacts/view");
        },
    );
  }
  
  onInvoiceView(InvoiceID: string) {
       this.invoiceService.SingleInvoice(InvoiceID).then(
        res => {
          localStorage.setItem('invoice', JSON.stringify(res));
          this.router.navigateByUrl("business/invoices/view");
        },
    );
  }
  
   create_invoice(){
       localStorage.removeItem('contact');
      this.router.navigateByUrl("/business/invoices/create");
  }
  import_invoice(){
    localStorage.removeItem('contact');
    this.router.navigateByUrl("/business/invoices/import");

  }
  change_status(status:string, InvoiceID:string){
    this.invoiceService.updateInvoice({Invoice:{Status:status}}, InvoiceID)
      .then(res => {
        swal("Success", res.message,'success');
        this.refresh();

      }, rej => {
        swal("Error", rej.message,'error');
      });
  }
  make_payment(InvoiceID:string){
    this.invoiceService.SingleInvoice(InvoiceID).then(
      res => {
        localStorage.setItem('invoice', JSON.stringify(res));
        this.dialog.open(PaymentFormComponent)
          .afterClosed()
          .subscribe(res => {
            this.refresh();
          });
      }
    )

  }
  export_invoice(){}

}
