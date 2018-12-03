import { Injectable } from '@angular/core';
import {HttpClient} from '@angular/common/http';
import {api_path} from '../../environments/global';
import {Observable} from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class InvoicesService {
  
  contact_invoice_api_path = `${api_path}invoice`;
  invoice_api_path = `${api_path}invoices`;
  
  constructor(private http: HttpClient) { }
  
  contactInvoices(contactID : string ) : Observable<any>{
      return this.http.get<any>(`${this.contact_invoice_api_path}/${contactID}`);
  }
  getAllInvoices(){
        return this.http.get<any>(`${this.invoice_api_path}`);
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
}
