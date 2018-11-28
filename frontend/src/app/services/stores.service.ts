import { Injectable } from '@angular/core';
import {HttpClient, HttpHeaders} from '@angular/common/http';

import {AuthService} from './auth.service';
import {Store} from '../models/store.model';
import {Observable} from 'rxjs';
import 'rxjs-compat/add/operator/map';
import {api_path} from '../../environments/global';



@Injectable({
  providedIn: 'root'
})
export class StoresService {

  private store: Store = null;
  protected store_url: string = api_path + 'user/stores';
  protected parent_store: string = api_path + 'user/stores/parent';
  private headers: HttpHeaders;

  constructor(protected http: HttpClient, protected authservice: AuthService,   ) {
    const permissions: string[] = authservice.getCurrentUser().permissions;
    if (permissions.indexOf('app_admin') == -1 ) {
      this.store_url = `${this.store_url}/${authservice.getCurrentUser().id}`;
      this.parent_store = `${this.parent_store}/${authservice.getCurrentUser().id}`;
    } else {
      this.parent_store = `${api_path}/stores/parent/0`;
      this.parent_store = `${api_path}/stores`;
    }

    this.headers = new HttpHeaders();
    this.headers.append('Content-type', 'application/json')
      .append('Accept', 'application/json');
  }

  public getStores(): Observable<Store[]> {
    return this.get(this.store_url);
  }
  public getParentStore(): Observable<Store[]> {
    return this.get(this.parent_store);
  }

  private get(url: string): Observable<Store[]> {
    return this.http.get<Store[]>(url);
  }

  public setStore(store: Store) {
    this.store = store;
  }

  public getStore(): Store | null {
    return this.store;
  }

  public initializeStore(): void {
    this.store = null;
  }
  public bulk_delete(param: Store[]) {
    const promise = new Promise((resolve, reject) => {
      this.http.post(api_path + 'user/stores/bulk_delete', param)
        .toPromise()
        .then(
          res  => {
            resolve(res);
          },
          error => {
            reject(error);
          }
        );
    });
    return promise;
  }
  public delete(id: number):  Promise<any>  {
    const promise = new Promise((resolve, reject) => {
      this.http.delete(api_path + 'user/stores/' + id)
        .toPromise()
        .then(
          res  => {
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
