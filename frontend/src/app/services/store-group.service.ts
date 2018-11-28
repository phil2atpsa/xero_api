import { Injectable } from '@angular/core';
import {HttpClient, HttpHeaders} from '@angular/common/http';
import {AuthService} from './auth.service';
import {Observable} from 'rxjs';

import {StoreGroup} from '../models/store-group.model';
import {StoresService} from './stores.service';
import {Store} from '../models/store.model';
import {api_path} from '../../environments/global';

@Injectable({
  providedIn: 'root'
})
export class StoreGroupService  {

  private store_group_url: string = api_path + 'user/stores/parent';
  private headers: HttpHeaders;
  private store_group: StoreGroup = null;

  constructor(protected http: HttpClient, protected authservice: AuthService) {
    const permissions: string[] = authservice.getCurrentUser().permissions;
    if (permissions.indexOf('app_admin') === -1 ) {
      this.store_group_url = `${this.store_group_url}/${authservice.getCurrentUser().id}`;
    }
    this.headers = new HttpHeaders();
    this.headers.append('Content-type', 'application/json')
      .append('Accept', 'application/json');
  }


  public list_store_groups(): Observable<StoreGroup[]> {
    return this.http.get<StoreGroup[]>(this.store_group_url);
  }

  public setStoreGroup(store_group: StoreGroup) : void {
    this.store_group = store_group;
  }

  public getStoreGroup(): StoreGroup | null {
    return this.store_group;
  }

  initializeStore(): void {
    this.store_group = null;
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


  public bulk_delete(param: StoreGroup[]) {
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
}
