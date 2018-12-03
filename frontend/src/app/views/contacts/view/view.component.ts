import { Component, OnInit } from '@angular/core';
import {Router} from "@angular/router";
import {InvoicesService} from "../../../services/invoices.service";
import {Contact} from "../../../models/contact.model";
@Component({
  selector: 'app-view',
  templateUrl: './view.component.html',
  styleUrls: ['./view.component.scss']
})
export class ViewComponent implements OnInit {
   
  invoices : any[]; 
  contact : any;
  isCollapsed = false;
  iconCollapse = 'icon-arrow-up';

  constructor(private router : Router, private invoiceService: InvoicesService) { }

  ngOnInit() {
      if(localStorage.getItem("contact") != null) {
        this.contact = JSON.parse(localStorage.getItem("contact"));
         this.invoiceService.contactInvoices(this.contact.ContactID).subscribe(
            list => {
                const array = list.map(
                item => {
                  return {...item};
                });
                 this.invoices = array;
            }
           
            
         );
      } else {
          this.router.navigateByUrl("/contacts");
      }
     
      
  }
  
  collapsed(event: any): void {
    // console.log(event);
  }

  expanded(event: any): void {
    // console.log(event);
  }

  toggleCollapse(): void {
    this.isCollapsed = !this.isCollapsed;
    this.iconCollapse = this.isCollapsed ? 'icon-arrow-down' : 'icon-arrow-up';
  }
  onViewInvoice(InvoiceID: string){
       this.invoiceService.SingleInvoice(InvoiceID).then(
        res => {
          localStorage.setItem('invoice', JSON.stringify(res));
          this.router.navigateByUrl("business/invoices/view");
        },
    );
  }
  
  create_invoice(){
      this.router.navigateByUrl("/business/invoices/create");
  }
  
  

}
