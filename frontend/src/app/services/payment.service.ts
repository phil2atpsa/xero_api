import { Injectable } from '@angular/core';
import {HttpClient, HttpHeaders} from "@angular/common/http";
import {api_path} from '../../environments/global';
import {Observable} from "rxjs";

@Injectable({
  providedIn: 'root'
})
export class PaymentService {
  private api_url = `${api_path}payments`;
  
  constructor(private  http: HttpClient) { }
  
  getList() : Observable<any[]> {
    return this.http.get<any[]>(this.api_url);
  }
  

  
  make_payment(post:any):Promise<any>{
       const promise:Promise<any> = new Promise<any>((resolve, reject) => {
        this.http.post(this.api_url, post)
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
