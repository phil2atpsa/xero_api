import { Component, OnInit } from '@angular/core';
import {MatDialogRef} from "@angular/material";
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import {CollectionsService} from "../../../../services/collections.service";
import swal from 'sweetalert2';

@Component({
  selector: 'app-collections-edit-form',
  templateUrl: './collections-edit-form.component.html',
  styleUrls: ['./collections-edit-form.component.scss']
})
export class CollectionsEditFormComponent implements OnInit {

  form: FormGroup;
  collection: any;
  loading:boolean = false;

  constructor( protected formBuilder: FormBuilder,
               public matDialogRef: MatDialogRef<CollectionsEditFormComponent>,
               protected service: CollectionsService) { }

  ngOnInit() {

    this.form = this.formBuilder.group({
      contact: [''],
      date:[''],
      policy_number:[''],
      collection_amount:['']
    });

    this.collection = JSON.parse(localStorage.getItem('collection'));

    this.form.patchValue({contact: this.collection.contact});
    this.form.get('contact').disable();

    this.form.patchValue({date: new Date(this.collection.created_at).toISOString().substring(0,10)});
    this.form.patchValue({policy_number: this.collection.policy_number});

    this.form.patchValue({collection_amount:this.collection.collection_amount});
    this.form.get('collection_amount').disable();


  }
  onCloseOperation(){
    this.matDialogRef.close();
  }
  save(){
    this.loading = true;
    let params = {
      date : this.form.value.date,
      reference : this.form.value.policy_number
    }
    this.service.collection_change(this.collection.id, params).then(
       res => {
         swal('Success', res.message, 'success');
         localStorage.removeItem('collection');
         this.onCloseOperation();
         this.loading = false;
       },
       rej => {
         swal('Error', rej.message, 'error');
         this.loading = false;
       }
    );
  

  }


}
