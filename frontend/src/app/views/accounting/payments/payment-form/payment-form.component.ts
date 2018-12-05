import { Component, OnInit } from '@angular/core';
import {AccountsService} from "../../../../services/accounts.service";
import {InvoicesService} from "../../../../services/invoices.service";
import {PaymentService} from "../../../../services/payment.service";
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import {MatDialogRef} from '@angular/material';
import swal from 'sweetalert2';

@Component({
  selector: 'app-payment-form',
  templateUrl: './payment-form.component.html',
  styleUrls: ['./payment-form.component.scss']
})
export class PaymentFormComponent implements OnInit {

  selected_invoice:any;
  selected_account:any;
  invoices:any[];
  accounts:any[];
  payment_form:FormGroup;
  submitted = false;
  loading=false;
  

  constructor(
  private invoiceService: InvoicesService, 
  private accountService: AccountsService,
  private formBuilder: FormBuilder, 
  private paymentService: PaymentService, 
  public matDialogRef: MatDialogRef<PaymentFormComponent>) { }

  ngOnInit() {

    this.selected_invoice = JSON.parse(localStorage.getItem('invoice'));

    this.accountService.getList().subscribe(
      list => {
        this.accounts = list.map(
          item => {
            // console.log(item);
            return {...item};
          });
        
      }
    );
     this.invoiceService.InvoivePayable().subscribe(
      list => {
        this.invoices = list.map(
          item => {
            // console.log(item);
            return {...item};
          });
        
      }
    );
    this.payment_form = this.formBuilder.group({
       invoice:['', Validators.required],
       account:['', Validators.required],
       date: ['', Validators.required],
       reference: ['',Validators.required],
       amount: ['', Validators.required],
    });
    
    if(this.selected_invoice != null) {
         this.payment_form.patchValue({invoice: this.selected_invoice });
      this.payment_form.patchValue({amount: this.selected_invoice.Total});
         this.payment_form.controls.invoice.disable();
    }
     this.payment_form.patchValue({date: this.currentDate()});
   //  this.invoice_form.controls.date.disable();
  }
  
   currentDate() {
        const currentDate = new Date();
        return currentDate.toISOString().substring(0,10);
    }
    
     get f() { return this.payment_form.controls; }
     
         onCloseOperation() {
        this.matDialogRef.close();
        this.payment_form.reset();
    }
     save(){
         this.submitted = true;
         if(this.payment_form.invalid){
             return;
         }
         this.loading =true;
        // alert(this.selected_invoice.InvoiceID);
         const InvoiceID = this.selected_invoice != null ? this.selected_invoice.InvoiceID :
           this.payment_form.value.invoice.InvoiceID;

         const post = 
             {
                 Payment :
                     {
                    Amount :   this.payment_form.value.amount,
                    Date : this.payment_form.value.date,
                    AccountID:  this.payment_form.value.account.AccountID,
                    InvoiceID: InvoiceID,
                    reference : this.payment_form.value.reference
                     }
            }
         
         this.paymentService.make_payment(post).then(
            success => {
                 swal('Success!', success.message,'success');
                 this.onCloseOperation();
            },  error => {
                 console.log(error);
                 swal('Error!', error.message,'error');
                 this.loading = false;
            }
        );
     }

}
