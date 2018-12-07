import { Component, OnInit } from '@angular/core';
import {api_path} from 'environments/global';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import {InvoicesService} from "../../../../services/invoices.service";

import swal from 'sweetalert2';
import {Router} from "@angular/router";





@Component({
  selector: 'app-import-invoice',
  templateUrl: './import-invoice.component.html',
  styleUrls: ['./import-invoice.component.scss']
})

export class ImportInvoiceComponent implements OnInit {

  file_path = `${api_path}storage/invoice`;
  import_form: FormGroup;
  submitted = false;
  loading = false;

  private template_file: File = null;
  constructor(private formBuilder: FormBuilder, private invoicesService: InvoicesService, private router:Router) { }

  ngOnInit() {
    this.import_form = this.formBuilder.group({
      template_file: ['', Validators.required],
      update_contact : ['']
    });
  }

  fileUploaded(event){
    this.template_file = <File> event.target.files[0];
  }

  get f() { return this.import_form.controls;}

    import_invoices(){
    this.submitted = true;
    if(this.import_form.invalid){
     return;
    }
    this.loading = true;


    this.invoicesService.upload_invoices(this.template_file,"data",JSON.stringify({}), "template_file")
      .then(res => {
        if(res.success) {
            swal('Success', res.message , 'success');
            const $router = this.router;
            this.loading = false;
            setTimeout(function () {
               $router.navigate(['business/invoices']);
            }, 3000) ;
        } else {
            swal('Error', res.message , 'error');
            this.loading = false;
        }


      }, rejected => {
        
        swal('Error', rejected.message , 'error');
        this.loading = false;
      });

  }

}
