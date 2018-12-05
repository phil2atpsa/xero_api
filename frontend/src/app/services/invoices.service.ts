import { Injectable } from '@angular/core';
import {HttpClient} from '@angular/common/http';
import {api_path} from '../../environments/global';
import {Observable} from 'rxjs';
import {FileUploadService} from "./file-upload.service";

@Injectable({
  providedIn: 'root'
})
export class InvoicesService {
  
  contact_invoice_api_path = `${api_path}invoice`;
  invoice_api_path = `${api_path}invoices`;
  invoice_upload_path = `${api_path}upload_invoices`;

  
  constructor(private http: HttpClient, private fileUploadService: FileUploadService) { }
  
  contactInvoices(contactID : string ) : Observable<any>{
      return this.http.get<any>(`${this.contact_invoice_api_path}/${contactID}`);
  }
  getAllInvoices(){
        return this.http.get<any>(`${this.invoice_api_path}`);
  }
  
  InvoivePayable(){
       return this.http.get<any>(`${api_path}payable-invoice`);
  }
  
  SingleInvoice(InvoiceID : string) : Promise<any> {


    const promise  = new Promise<any>((resolve, reject) => {
      this.http.get(`${this.invoice_api_path}/${InvoiceID}`)
        .toPromise()
        .then(
          res => {
            resolve(res);
          },
          error => {
            reject(error);
          }
        );
    });
    return promise;

  }

  createInvoice(post : any, contactID: string) : Promise<any> {
    const promise  = new Promise<any>((resolve, reject) => {
      this.http.post(`${this.contact_invoice_api_path}/${contactID}`, post)
        .toPromise()
        .then(
          res => {
            resolve(res);
          },
          error => {
            reject(error);
          }
        );
    });
    return promise;

  }

  updateInvoice(post : any, InvoiceID: string) : Promise<any> {
    const promise  = new Promise<any>((resolve, reject) => {
      this.http.put(`${this.invoice_api_path}/${InvoiceID}`, post)
        .toPromise()
        .then(
          res => {
            resolve(res);
          },
          error => {
            reject(error);
          }
        );
    });
    return promise;

  }

  upload_invoices(file: File, data_key: string,  data: string, input_name: string) : Promise<any>{
    return this.fileUploadService.uploadFileToUrl(this.invoice_upload_path,file,data_key,data,input_name);
  }

}
