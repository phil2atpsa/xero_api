import { Injectable } from '@angular/core';
import { api_path } from './../../environments/global';
import { Observable } from 'rxjs';
import { HttpClient } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class UnallocatedService {
  api_url = `${api_path}unallocated-payments`;

  constructor(private http: HttpClient) { }

  getPayments(): Observable<any[]> {
      return this.http.get<any[]>(this.api_url);
  }

  allocate_payment(paymentID : number) : Promise<any>{
    const promise:Promise<any> = new Promise<any>(
      (resolve, reject) => {
        this.http.put(`${this.api_url}/${paymentID}`, {})
          .toPromise()
          .then(
            res => {
              resolve(res)
            },
            rej => {
              reject(rej)
            }
          )
      }
    );
    return promise;
  }


}
