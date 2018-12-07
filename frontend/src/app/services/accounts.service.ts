import { Injectable } from '@angular/core';
import {HttpClient, HttpHeaders} from '@angular/common/http';
import {api_path} from '../../environments/global';
import {Observable} from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class AccountsService {
  private api_url = `${api_path}accounts`;
  private account_type_api_url = `${api_path}account-types`;
  private account_classes_api_url = `${api_path}account-classes`;

  constructor(private  http: HttpClient) { }

  getList(): Observable<any[]> {
    return this.http.get<any[]>(this.api_url);
  }

  account_types(): Observable<any[]> {
    return this.http.get<any[]>(this.account_type_api_url);
  }

  account_classes(): Observable<any[]> {
    return this.http.get<any[]>(this.account_classes_api_url);
  }

  create(post: any): Promise<any> {
       const promise: Promise<any> = new Promise<any>((resolve, reject) => {
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
