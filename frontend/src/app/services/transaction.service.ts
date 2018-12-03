import { Injectable } from '@angular/core';
import {HttpClient, HttpHeaders} from "@angular/common/http";
import {api_path} from '../../environments/global';
import {Observable} from "rxjs";

@Injectable({
  providedIn: 'root'
})
export class TransactionService {
  private api_url = `${api_path}bank-transactions`;
 
  
  constructor(private  http: HttpClient) { }
  
  getList() : Observable<any[]> {
    return this.http.get<any[]>(this.api_url);
  }
  

  
  create(post:any, transID:string|null):Promise<any>{
      let  promise:Promise<any>  = null;
      if(transID == null) {
       promise = new Promise<any>((resolve, reject) => {
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
      } else {
           promise = new Promise<any>((resolve, reject) => {
        this.http.put(`${this.api_url}/${transID}`, post)
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
      }
    return promise;
  }
}
