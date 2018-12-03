
import { Component, OnInit } from '@angular/core';
import {AccountsService} from '../../../../services/accounts.service';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import {MatDialogRef} from '@angular/material';

import * as _ from  'lodash';


import swal from 'sweetalert2';

@Component({
  selector: 'app-account-form',
  templateUrl: './account-form.component.html',
  styleUrls: ['./account-form.component.scss']
})
export class AccountFormComponent implements OnInit {
  account_form: FormGroup; 
  account_types:any[];
  account_class:any[];
  selected_account_type:string;
  submitted=false;
  loading=false;
  
    
  constructor(private accountService: AccountsService, private formBuilder: FormBuilder, public matDialogRef: MatDialogRef<AccountFormComponent>) { }

  ngOnInit() {
      this.accountService.account_types().subscribe(
      list => {
        this.account_types = list.map(
          item => {
            // console.log(item);
            return {...item};
          });
         console.log( this.account_types);
      }
    );
    
     this.accountService.account_classes().subscribe(
      list => {
        this.account_classes = list.map(
          item => {
            // console.log(item);
            return {...item};
          });
        
      }
    );
     this.account_form = this.formBuilder.group({
       code:['', Validators.required],
       name:['', Validators.required],
       account_type: ['', Validators.required],
       bank_account_number: [''],
       account_class: ['', Validators.required],
   

    });
  }
  
  
  onCloseOperation() {
    this.matDialogRef.close();
    this.account_form.reset();
  }
  onSelectType(account_type:any){
      this.selected_account_type = account_type.name;
  }
  
  save_account(){
      this.submitted =  true;
      if(this.account_form.invalid){
          return;
      }
      this.loading = true;
      const post = {
          Code: this.account_form.value.code,
          Name: this.account_form.value.name,
          Type: this.selected_account_type,
          Class:  this.account_form.value.account_class.name,
          BankAccountNumber:  this.account_form.value.bank_account_number,
          CurrencyCode : 'ZAR'
           
      }
      
      
        this.accountService.create(post).then(
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
  
  
    get f() { return this.account_form.controls; }

}
