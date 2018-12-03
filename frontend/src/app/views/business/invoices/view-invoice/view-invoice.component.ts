import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-view-invoice',
  templateUrl: './view-invoice.component.html',
  styleUrls: ['./view-invoice.component.scss']
})
export class ViewInvoiceComponent implements OnInit {
  
  invoice : any;
  constructor() { }

  ngOnInit() {
       if(localStorage.getItem("invoice") != null) {
        this.invoice = JSON.parse(localStorage.getItem("invoice"));
       }
  }

}
