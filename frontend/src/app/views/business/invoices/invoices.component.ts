import { Component, OnInit, ViewChild } from '@angular/core';
import {InvoicesService} from "../../../services/invoices.service";
import {ContactService} from "../../../services/contact.service";
import {MatSort, MatTableDataSource,MatPaginator} from "@angular/material";
import {Router} from "@angular/router";

@Component({
  selector: 'app-invoices',
  templateUrl: './invoices.component.html',
  styleUrls: ['./invoices.component.scss']
})
export class InvoicesComponent implements OnInit {
  list: MatTableDataSource<any>;
  searchKey ='';
 // c_all = false;
  displayedColumns: string[] = [
    'contact',
    'invoice_number',
    'reference',
    'date',
    'duedate',
    'status',
    'total',
    'invoice_actions'
  ];

  @ViewChild(MatSort) sort: MatSort;
  @ViewChild(MatPaginator) paginator: MatPaginator;   
  constructor(private invoiceService: InvoicesService,private contactService: ContactService, private router: Router) { }

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

}
