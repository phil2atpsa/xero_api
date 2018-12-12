import { Component, OnInit,ViewChild } from '@angular/core';
import {Contact} from "../../../../models/contact.model";
import {FormBuilder, FormGroup, Validators,FormArray} from '@angular/forms';
import {ContactService} from "../../../../services/contact.service";
import {InvoicesService} from "../../../../services/invoices.service";
import swal from 'sweetalert2';
import {Router} from "@angular/router";


@Component({
  selector: 'app-form-invoice',
  templateUrl: './form-invoice.component.html',
  styleUrls: ['./form-invoice.component.scss']
})



export class FormInvoiceComponent implements OnInit {
    selected_contact: any = null;
    contacts: any[];
    invoice_form: FormGroup; 
    line_items:FormArray;
    subtotal:number = 0;
    total_tax:number = 0;
    total:number = 0;
    amount_due:number = 0;
    submitted=false;
    loading = false;
    
    
    
 
  constructor(private contactService:ContactService, private formBuilder: FormBuilder,
   private invoiceService:InvoicesService, private router: Router) { }

  ngOnInit() {
     
        this.contactService.getContacts().subscribe(
      list => {
        const array = list.map(
          item => {
            // console.log(item);
            return {...item};
          });
         this.contacts = array;
      }
    );
     this.invoice_form = this.formBuilder.group({
         contact: ['', Validators.required],
         line_items: this.formBuilder.array([ this.createItem()]),
         duedate:['', Validators.required],
         invoice_number:['', Validators.required],
         ref_number:['', Validators.required],
         date:['', Validators.required]
    });
    
      
    
     
     if(localStorage.getItem("contact") != null) {
          this.selected_contact  = JSON.parse(localStorage.getItem("contact")); 
          this.invoice_form.patchValue({invoice_number: this.generateInvoiceNumber( this.selected_contact.Name)});
          this.invoice_form.patchValue({contact:  this.selected_contact});
          this.invoice_form.controls.contact.disable();
      } else {
           this.invoice_form.controls.contact.enable();
      }
      
      this.invoice_form.patchValue({date: this.currentDate()});
  }
  createItem(): FormGroup {
        return this.formBuilder.group({
          description: '',
          quantity: '',
          unitAmount: '',
          lineTax: '',
          lineAmount: ''
        });
    }
    
   onContactChange() {
      // alert(this.invoice_form.value.contact.Name);
       this.invoice_form.patchValue({invoice_number: this.generateInvoiceNumber( this.invoice_form.value.contact.Name)});
       this.selected_contact = this.invoice_form.value.contact;
   }
    
   addItem(): void {
       this.line_items =  <FormArray>this.invoice_form.controls['line_items'];
       this.line_items.push(this.createItem());
       //this.calculate_total();
       
      
       
    }
    removeItem(index:number): void{
        const control = <FormArray>this.invoice_form.controls['line_items'];
        control.removeAt(index);
         this.calculate_total();
    }
    makeLine(index:number){
   //  detailsForm.controls['business_type']['controls']
       const quantity =  parseInt(this.invoice_form.controls['line_items']['controls'][index].controls.quantity.value);
       const unitAmount =  parseFloat(this.invoice_form.controls['line_items']['controls'][index].controls.unitAmount.value);
       const _line = quantity * unitAmount;
       const _tax = 0.15 * _line;
       
       const lineTax =  this.invoice_form.controls['line_items']['controls'][index].controls.lineTax;
       const lineAmount =  this.invoice_form.controls['line_items']['controls'][index].controls.lineAmount;
     //  alert( typeof(lineAmount));
       if(!isNaN(_line) && !isNaN(_tax) && !isNaN(this.subtotal)){
        lineTax.setValue(_tax.toFixed(2));
        lineAmount.setValue(_line.toFixed(2));
        this.calculate_total();
       }
    }
    
     calculate_total() : void {
        this.total_tax = 0;
        this.subtotal = 0;
       
       this.invoice_form.controls['line_items']['controls'].map((item, index)=>{
             this.total_tax +=  parseFloat(this.invoice_form.controls['line_items']['controls'][index].controls.lineTax.value);
             this.subtotal +=  parseFloat(this.invoice_form.controls['line_items']['controls'][index].controls.lineAmount.value);
            
        });
       
       this.total =this.total_tax +   this.subtotal;
       this.amount_due = this.total;
    }
    
     currentDate() {
        const currentDate = new Date();
        return currentDate.toISOString().substring(0,10);
    }
    
   generateInvoiceNumber(contact_name: string):string{
        return contact_name.substr(0,2).toUpperCase()+"-"+this.getRandomInt(5555,9999);
    }
    
    
    
   form_post (status: string): any {
         const post = {
            Invoice: {
                Type: 'ACCREC',
                Date: this.invoice_form.value.date,
                DueDate: this.invoice_form.value.duedate,
                InvoiceNumber: this.invoice_form.value.invoice_number,
                Reference: this.invoice_form.value.ref_number,
                CurrencyCode:'ZAR',
                Status: status,
                LineItems:[]
            }
        }
         this.invoice_form.controls['line_items']['controls'].map( (item, index) =>{
            const LineItem = {
                Description : this.invoice_form.controls['line_items']['controls'][index].controls.description.value,
                Quantity : this.invoice_form.controls['line_items']['controls'][index].controls.quantity.value,
                UnitAmount: this.invoice_form.controls['line_items']['controls'][index].controls.unitAmount.value,
                TaxAmount: this.invoice_form.controls['line_items']['controls'][index].controls.lineTax.value,
                LineAmount: this.invoice_form.controls['line_items']['controls'][index].controls.lineAmount.value,
                TaxType : "OUTPUT",
                AccountCode: "200"
            }
            post.Invoice.LineItems.push(LineItem);
            
        });
        
        return post;
    }
    
    save_invoice(){
        this.submitted = true;
        this.loading=true;
        
        this.invoiceService.createInvoice(this.form_post('SUBMITTED'), this.selected_contact.ContactID).then(
            success => {
                 swal('Success!', success.message,'success');
                 const $router = this.router;
                
                 setTimeout(function(){
                     $router.navigate(['/business/invoices'])
                 },2000);
            },  error => {
                 swal('Error!', error.message,'error');
                 this.loading = false;
            }
        );
       
       
        
    }
    
    save_draft(){
         this.submitted = true;
    
         this.loading=true;
           this.invoiceService.createInvoice(this.form_post('DRAFT'), this.selected_contact.ContactID).then(
            success => {
                 swal('Success!', success.message,'success');
                 const $router = this.router;
                 setTimeout(function(){
                     $router.navigate(['/business/invoices'])
                 },2000);
            },  error => {
                 swal('Error!', error.message,'error');
                 this.loading=false;
            }
        );
    }
    
    
     get f() { return this.invoice_form.controls; }
    
    getRandomInt(min:number, max:number): number {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }
  
  

}
